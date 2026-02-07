<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AgentTargetResource;
use App\Models\AgentTarget;
use App\Models\AgentTargetItem;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgentTargetController extends Controller
{
    public function index(Request $request)
    {
        $query = AgentTarget::with('items');

        if ($request->has('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }

        if ($request->has('period_month')) {
            $query->where('period_month', $request->period_month);
        }

        $targets = $query->get();

        return AgentTargetResource::collection($targets);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'staff_id' => 'required|exists:sales_agents,id',
            'period_month' => 'required|date_format:Y-m',
            'target_type' => 'required|in:product,supplier,category',
            'target_qty' => 'required|numeric|min:0',
            'reward_per_unit_iqd' => 'required|numeric|min:0',
            'min_achievement_percent' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
            'items' => 'required|array|min:1',
            'items.*.reference_type' => 'required|in:product,supplier,category',
            'items.*.reference_id' => 'required|integer',
        ]);

        $target = DB::transaction(function () use ($validated) {
            $target = AgentTarget::create([
                'staff_id' => $validated['staff_id'],
                'period_month' => $validated['period_month'],
                'target_type' => $validated['target_type'],
                'target_qty' => $validated['target_qty'],
                'reward_per_unit_iqd' => $validated['reward_per_unit_iqd'],
                'min_achievement_percent' => $validated['min_achievement_percent'],
                'is_active' => $validated['is_active'] ?? true,
            ]);

            foreach ($validated['items'] as $item) {
                AgentTargetItem::create([
                    'agent_target_id' => $target->id,
                    'reference_type' => $item['reference_type'],
                    'reference_id' => $item['reference_id'],
                ]);
            }

            return $target;
        });

        return response()->json([
            'message' => 'تم إنشاء الهدف بنجاح',
            'target' => new AgentTargetResource($target->load('items'))
        ], 201);
    }

    public function show(AgentTarget $agentTarget)
    {
        $agentTarget->load('items', 'results');
        return new AgentTargetResource($agentTarget);
    }

    public function update(Request $request, AgentTarget $agentTarget)
    {
        $validated = $request->validate([
            'target_qty' => 'sometimes|required|numeric|min:0',
            'reward_per_unit_iqd' => 'sometimes|required|numeric|min:0',
            'min_achievement_percent' => 'sometimes|required|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        $agentTarget->update($validated);

        return response()->json([
            'message' => 'تم تحديث الهدف بنجاح',
            'target' => new AgentTargetResource($agentTarget)
        ]);
    }

    public function destroy(AgentTarget $agentTarget)
    {
        // Delete related items
        $agentTarget->items()->delete();
        $agentTarget->delete();

        return response()->json([
            'message' => 'تم حذف الهدف بنجاح'
        ]);
    }

    public function calculateBonus(Request $request)
    {
        $validated = $request->validate([
            'agent_id' => 'required|exists:sales_agents,id',
            'period_month' => 'required|date_format:Y-m',
        ]);

        $agentId = $validated['agent_id'];
        $periodMonth = $validated['period_month'];

        // Get active targets for this agent and month
        $targets = AgentTarget::where('staff_id', $agentId)
            ->where('period_month', $periodMonth)
            ->where('is_active', true)
            ->with('items')
            ->get();

        if ($targets->isEmpty()) {
            return response()->json([
                'message' => 'لا توجد أهداف نشطة لهذا المندوب في هذا الشهر',
                'total_bonus' => 0,
                'targets' => []
            ]);
        }

        $results = [];
        $totalBonus = 0;

        foreach ($targets as $target) {
            $achievedQty = 0;

            // Calculate achieved quantity based on target type
            foreach ($target->items as $item) {
                $query = SalesInvoiceLine::whereHas('invoice', function ($q) use ($agentId, $periodMonth) {
                    $q->where('agent_id', $agentId)
                        ->where('status', 'delivered')
                        ->whereYear('invoice_date', substr($periodMonth, 0, 4))
                        ->whereMonth('invoice_date', substr($periodMonth, 5, 2));
                });

                if ($item->reference_type === 'product') {
                    $achievedQty += $query->where('product_id', $item->reference_id)->sum('qty');
                } elseif ($item->reference_type === 'supplier') {
                    $achievedQty += $query->whereHas('product', function ($q) use ($item) {
                        $q->whereHas('suppliers', function ($sq) use ($item) {
                            $sq->where('supplier_id', $item->reference_id);
                        });
                    })->sum('qty');
                } elseif ($item->reference_type === 'category') {
                    $achievedQty += $query->whereHas('product', function ($q) use ($item) {
                        $q->where('category_id', $item->reference_id);
                    })->sum('qty');
                }
            }

            $achievementPercent = ($achievedQty / $target->target_qty) * 100;
            $bonus = 0;

            if ($achievementPercent >= $target->min_achievement_percent) {
                $bonus = $achievedQty * $target->reward_per_unit_iqd;
                $totalBonus += $bonus;
            }

            $results[] = [
                'target_id' => $target->id,
                'target_type' => $target->target_type,
                'target_qty' => $target->target_qty,
                'achieved_qty' => $achievedQty,
                'achievement_percent' => round($achievementPercent, 2),
                'min_achievement_percent' => $target->min_achievement_percent,
                'reward_per_unit' => $target->reward_per_unit_iqd,
                'bonus' => round($bonus, 2),
                'qualified' => $achievementPercent >= $target->min_achievement_percent,
            ];
        }

        return response()->json([
            'agent_id' => $agentId,
            'period_month' => $periodMonth,
            'total_bonus' => round($totalBonus, 2),
            'targets' => $results,
        ]);
    }
}
