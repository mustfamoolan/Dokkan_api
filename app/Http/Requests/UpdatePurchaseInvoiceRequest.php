<?php

namespace App\Http\Requests;

use App\Models\PurchaseInvoice;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePurchaseInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Only allow if status is draft
        $invoice = $this->route('purchase_invoice');
        return $invoice && $invoice->status === 'draft';
    }

    public function rules(): array
    {
        return [
            'supplier_id' => 'required|exists:suppliers,id',
            'supplier_invoice_no' => 'nullable|string',
            'invoice_date' => 'required|date',
            'warehouse_id' => 'required|exists:warehouses,id',
            'currency' => 'required|in:IQD,USD',
            'exchange_rate' => 'required|numeric|min:1',
            'driver_fee' => 'numeric|min:0',
            'worker_fee' => 'numeric|min:0',
            'discount_foreign' => 'numeric|min:0',
            'notes' => 'nullable|string',
            'lines' => 'required|array|min:1',
            'lines.*.product_id' => 'required|exists:products,id',
            'lines.*.unit_id' => 'required|exists:units,id',
            'lines.*.unit_factor' => 'numeric|min:0.0001',
            'lines.*.qty' => 'required|numeric|min:0.01',
            'lines.*.price_foreign' => 'required|numeric|min:0',
            'lines.*.sale_price_retail' => 'numeric|min:0',
            'lines.*.sale_price_wholesale' => 'numeric|min:0',
            'lines.*.is_free' => 'boolean',
        ];
    }
}
