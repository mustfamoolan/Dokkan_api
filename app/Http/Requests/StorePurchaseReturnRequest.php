<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_invoice_id' => 'nullable|exists:purchase_invoices,id',
            'return_date' => 'required|date',
            'currency' => 'required|in:IQD,USD',
            'exchange_rate' => 'required|numeric|min:1',
            'lines' => 'required|array|min:1',
            'lines.*.product_id' => 'required|exists:products,id',
            'lines.*.unit_id' => 'required|exists:units,id',
            'lines.*.unit_factor' => 'numeric|min:0.0001',
            'lines.*.qty' => 'required|numeric|min:0.01',
            'lines.*.price_foreign' => 'required|numeric|min:0',
        ];
    }
}
