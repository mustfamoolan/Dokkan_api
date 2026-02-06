<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePurchaseReturnRequest;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseReturnController extends Controller
{
    public function store(StorePurchaseReturnRequest $request)
    {
        $return = DB::transaction(function () use ($request) {
            $currency = $request->currency;
            $exchangeRate = $request->exchange_rate;

            $totalForeign = 0;

            foreach ($request->lines as $lineData) {
                $qty = $lineData['qty'];
                $price = $lineData['price_foreign'];
                $totalForeign += ($qty * $price);
            }

            $totalIqd = $totalForeign * $exchangeRate;

            $return = PurchaseReturn::create([
                'return_no' => 'PR-' . time(),
                'supplier_id' => $request->supplier_id,
                'purchase_invoice_id' => $request->purchase_invoice_id,
                'return_date' => $request->return_date,
                'currency' => $currency,
                'exchange_rate' => $exchangeRate,
                'total_foreign' => $totalForeign,
                'total_iqd' => $totalIqd,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            foreach ($request->lines as $lineData) {
                $qty = $lineData['qty'];
                $price = $lineData['price_foreign']; // Unit price in Foreign currency

                // Calculate Line Total IQD for the line record (model has line_total_iqd)
                $lineTotalIqd = ($qty * $price) * $exchangeRate;

                PurchaseReturnLine::create([
                    'purchase_return_id' => $return->id,
                    'product_id' => $lineData['product_id'],
                    'qty' => $qty,
                    'unit_id' => $lineData['unit_id'],
                    'unit_factor' => $lineData['unit_factor'],
                    'price_foreign' => $price,
                    'line_total_iqd' => $lineTotalIqd,
                ]);
            }

            return $return;
        });

        return response()->json(['message' => 'تم إنشاء مسودة المرتجع', 'return' => $return->load('lines')], 201);
    }

    public function post(PurchaseReturn $purchaseReturn)
    {
        if ($purchaseReturn->status !== 'draft') {
            return response()->json(['message' => 'المرتجع ليس مسودة'], 400);
        }

        // Observer handles logic
        $purchaseReturn->update([
            'status' => 'posted',
            'approved_by' => auth()->id(),
        ]);

        return response()->json(['message' => 'تم ترحيل المرتجع', 'return' => $purchaseReturn]);
    }
}
