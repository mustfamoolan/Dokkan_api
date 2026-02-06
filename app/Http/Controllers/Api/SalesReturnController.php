<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SalesReturn;
use App\Models\SalesReturnLine;
use App\Models\SalesInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesReturnController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['sales_invoice_id' => 'required|exists:sales_invoices,id']);

        $return = DB::transaction(function () use ($request) {
            $totalIqd = 0;
            foreach ($request->lines as $line) {
                $totalIqd += ($line['qty'] * $line['price_iqd']);
            }

            $return = SalesReturn::create([
                'return_no' => 'SR-' . time(),
                'sales_invoice_id' => $request->sales_invoice_id,
                'return_date' => now(),
                'total_iqd' => $totalIqd,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            foreach ($request->lines as $line) {
                // Try to get original cost snapshot from invoice line if possible? 
                // For now user passes or we just record return basic details
                // We can fetch product cost using Product logic if desired.

                SalesReturnLine::create([
                    'sales_return_id' => $return->id,
                    'product_id' => $line['product_id'],
                    'qty' => $line['qty'],
                    'unit_id' => $line['unit_id'],
                    'unit_factor' => $line['unit_factor'] ?? 1,
                    'price_iqd' => $line['price_iqd'],
                    'line_total_iqd' => $line['qty'] * $line['price_iqd'],
                    // cost_iqd_snapshot -> passed or calculated? leaving 0 for now
                ]);
            }
            return $return;
        });

        return response()->json(['message' => 'Return drafted', 'return' => $return], 201);
    }

    public function post(SalesReturn $return)
    {
        if ($return->status !== 'draft')
            abort(400, 'Invalid status');
        $return->update(['status' => 'posted']);
        // Observer handles Inventory IN + Reverse Journal
        return response()->json(['message' => 'Return posted']);
    }
}
