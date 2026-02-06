<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOpeningBalanceRequest;
use App\Models\InventoryBalance;
use App\Models\InventoryTransaction;
use App\Models\InventoryTransactionLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function balances(Request $request)
    {
        $query = InventoryBalance::with(['product', 'warehouse']);

        if ($request->has('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->has('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        return response()->json($query->get());
    }

    public function transactions(Request $request)
    {
        $query = InventoryTransaction::with(['lines.product', 'creator', 'warehouse'])
            ->orderBy('trans_date', 'desc')
            ->orderBy('id', 'desc');

        if ($request->has('product_id')) {
            $query->whereHas('lines', function ($q) use ($request) {
                $q->where('product_id', $request->product_id);
            });
        }

        if ($request->has('date_from')) {
            $query->where('trans_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('trans_date', '<=', $request->date_to);
        }

        return response()->json($query->paginate(20));
    }

    public function openingBalance(StoreOpeningBalanceRequest $request)
    {
        $transaction = DB::transaction(function () use ($request) {
            // 1. Create Transaction Header
            $trans = InventoryTransaction::create([
                'trans_date' => now(),
                'trans_type' => 'opening_balance',
                'warehouse_id' => $request->warehouse_id,
                'reference_type' => 'manual',
                'reference_id' => 0, // No external reference
                'created_by' => auth()->id(),
                'note' => 'رصيد افتتاحي',
            ]);

            foreach ($request->items as $item) {
                $unitFactor = $item['unit_factor'] ?? 1;
                $baseQty = $item['qty'] * $unitFactor;
                $costIqd = $item['cost_iqd']; // Per Unit

                // 2. Create Transaction Line
                InventoryTransactionLine::create([
                    'inventory_transaction_id' => $trans->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'unit_id' => $item['unit_id'],
                    'unit_factor' => $unitFactor,
                    'cost_iqd' => $costIqd,
                ]);

                // 3. Update Balance (Weighted Average)
                $balance = InventoryBalance::firstOrNew([
                    'warehouse_id' => $request->warehouse_id,
                    'product_id' => $item['product_id']
                ]);

                $oldQty = $balance->qty_on_hand ?? 0;
                $oldCost = $balance->avg_cost_iqd ?? 0;
                $newQty = $baseQty;
                // Cost is per unit passed in. Ensure we convert to cost per base unit?
                // Assuming input cost is cost per THE UNIT used.
                // Base Cost = input_cost / unit_factor
                $newBaseCost = $costIqd / $unitFactor;

                $totalValue = ($oldQty * $oldCost) + ($newQty * $newBaseCost);
                $totalQty = $oldQty + $newQty;

                $balance->qty_on_hand = $totalQty;
                $balance->avg_cost_iqd = $totalQty > 0 ? $totalValue / $totalQty : $newBaseCost;
                $balance->save();
            }

            return $trans;
        });

        return response()->json(['message' => 'تم حفظ الرصيد الافتتاحي', 'transaction' => $transaction->load('lines')], 201);
    }
}
