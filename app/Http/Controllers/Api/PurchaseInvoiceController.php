<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePurchaseInvoiceRequest;
use App\Http\Requests\UpdatePurchaseInvoiceRequest;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseInvoiceController extends Controller
{
    public function index()
    {
        // Pagination + filters can be added later
        $invoices = PurchaseInvoice::with(['supplier', 'creator'])
            ->orderBy('id', 'desc')
            ->paginate(20);
        return response()->json($invoices);
    }

    public function show(PurchaseInvoice $purchaseInvoice)
    {
        return response()->json($purchaseInvoice->load(['lines.product', 'lines.unit', 'supplier', 'creator', 'approvedBy', 'journalEntry']));
    }

    public function store(StorePurchaseInvoiceRequest $request)
    {
        $invoice = DB::transaction(function () use ($request) {
            $currency = $request->currency;
            $exchangeRate = $request->exchange_rate;

            $subtotalForeign = 0;

            // 1. Calculate Subtotal
            foreach ($request->lines as $lineData) {
                $qty = $lineData['qty'];
                $price = $lineData['price_foreign'];
                $isFree = $lineData['is_free'] ?? false;

                if (!$isFree) {
                    $subtotalForeign += ($qty * $price);
                }
            }

            $discountForeign = $request->discount_foreign ?? 0;
            $totalForeign = max(0, $subtotalForeign - $discountForeign);

            // Convert to IQD
            // If Currency is IQD, then total_foreign == total_iqd (exchange rate 1)
            // If Currency is USD, total_iqd = total_foreign * exchange_rate
            $totalIqd = $totalForeign * $exchangeRate;

            $invoice = PurchaseInvoice::create([
                'invoice_no' => 'PI-' . time(), // Simple serial generation
                'supplier_invoice_no' => $request->supplier_invoice_no,
                'supplier_id' => $request->supplier_id,
                'invoice_date' => $request->invoice_date,
                'currency' => $currency,
                'exchange_rate' => $exchangeRate,
                'subtotal_foreign' => $subtotalForeign,
                'discount_foreign' => $discountForeign,
                'total_foreign' => $totalForeign,
                'total_iqd' => $totalIqd,
                'status' => 'draft',
                'notes' => $request->notes,
                'created_by' => auth()->id(),
            ]);

            foreach ($request->lines as $lineData) {
                // Determine line total
                $qty = $lineData['qty'];
                $price = $lineData['price_foreign'];
                $isFree = $lineData['is_free'] ?? false;

                $lineTotalForeign = $isFree ? 0 : ($qty * $price);
                $lineTotalIqd = $lineTotalForeign * $exchangeRate;

                PurchaseInvoiceLine::create([
                    'purchase_invoice_id' => $invoice->id,
                    'product_id' => $lineData['product_id'],
                    'qty' => $qty,
                    'unit_id' => $lineData['unit_id'],
                    'unit_factor' => $lineData['unit_factor'],
                    'price_foreign' => $price,
                    'line_total_foreign' => $lineTotalForeign,
                    'line_total_iqd' => $lineTotalIqd,
                    'is_free' => $isFree,
                ]);
            }

            return $invoice;
        });

        return response()->json(['message' => 'تم إنشاء مسودة الفاتورة', 'invoice' => $invoice->load('lines')], 201);
    }

    public function update(UpdatePurchaseInvoiceRequest $request, PurchaseInvoice $purchaseInvoice)
    {
        // Only draft can be updated (middleware/request check handles authorization)

        $invoice = DB::transaction(function () use ($request, $purchaseInvoice) {
            $currency = $request->currency;
            $exchangeRate = $request->exchange_rate;
            $subtotalForeign = 0;

            // Delete old lines
            $purchaseInvoice->lines()->delete();

            foreach ($request->lines as $lineData) {
                $qty = $lineData['qty'];
                $price = $lineData['price_foreign'];
                $isFree = $lineData['is_free'] ?? false;

                if (!$isFree) {
                    $subtotalForeign += ($qty * $price);
                }

                $lineTotalForeign = $isFree ? 0 : ($qty * $price);
                $lineTotalIqd = $lineTotalForeign * $exchangeRate;

                PurchaseInvoiceLine::create([
                    'purchase_invoice_id' => $purchaseInvoice->id,
                    'product_id' => $lineData['product_id'],
                    'qty' => $qty,
                    'unit_id' => $lineData['unit_id'],
                    'unit_factor' => $lineData['unit_factor'],
                    'price_foreign' => $price,
                    'line_total_foreign' => $lineTotalForeign,
                    'line_total_iqd' => $lineTotalIqd,
                    'is_free' => $isFree,
                ]);
            }

            $discountForeign = $request->discount_foreign ?? 0;
            $totalForeign = max(0, $subtotalForeign - $discountForeign);
            $totalIqd = $totalForeign * $exchangeRate;

            $purchaseInvoice->update([
                'supplier_invoice_no' => $request->supplier_invoice_no,
                'supplier_id' => $request->supplier_id,
                'invoice_date' => $request->invoice_date,
                'currency' => $currency,
                'exchange_rate' => $exchangeRate,
                'subtotal_foreign' => $subtotalForeign,
                'discount_foreign' => $discountForeign,
                'total_foreign' => $totalForeign,
                'total_iqd' => $totalIqd,
                'notes' => $request->notes,
            ]);

            return $purchaseInvoice;
        });

        return response()->json(['message' => 'تم تحديث الفاتورة', 'invoice' => $invoice->load('lines')]);
    }

    public function approve(PurchaseInvoice $purchaseInvoice)
    {
        if ($purchaseInvoice->status !== 'draft') {
            return response()->json(['message' => 'الفاتورة ليست مسودة'], 400);
        }

        $purchaseInvoice->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
        ]);

        return response()->json(['message' => 'تمت الموافقة على الفاتورة', 'invoice' => $purchaseInvoice]);
    }

    public function post(PurchaseInvoice $purchaseInvoice)
    {
        // Allow posting from approved OR draft directly if policy allows. 
        // Assuming strict flow Draft -> Approved -> Posted, or Draft -> Posted. Let's allow both.
        if (!in_array($purchaseInvoice->status, ['draft', 'approved'])) {
            return response()->json(['message' => 'حالة الفاتورة لا تسمح بالترحيل'], 400);
        }

        // The Observer will handle the actual logic when status changes to 'posted'
        $purchaseInvoice->update([
            'status' => 'posted',
            'approved_by' => $purchaseInvoice->approved_by ?? auth()->id(), // ensure approved_by is set
        ]);

        // Reload to get journal entry ID if observer worked
        $purchaseInvoice->refresh();

        return response()->json([
            'message' => 'تم ترحيل الفاتورة بنجاح',
            'invoice' => $purchaseInvoice,
            'journal_entry_id' => $purchaseInvoice->journal_entry_id
        ]);
    }
}
