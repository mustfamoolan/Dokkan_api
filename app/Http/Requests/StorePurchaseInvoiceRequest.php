<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'supplier_id' => 'required|exists:suppliers,id',
            'supplier_invoice_no' => 'nullable|string',
            'invoice_date' => 'required|date',
            'currency' => 'required|in:IQD,USD',
            'exchange_rate' => 'required|numeric|min:1', // Default 1 if IQD
            'discount_foreign' => 'numeric|min:0',
            'notes' => 'nullable|string',
            'lines' => 'required|array|min:1',
            'lines.*.product_id' => 'required|exists:products,id',
            'lines.*.unit_id' => 'required|exists:units,id',
            'lines.*.unit_factor' => 'numeric|min:0.0001',
            'lines.*.qty' => 'required|numeric|min:0.01',
            'lines.*.price_foreign' => 'required|numeric|min:0',
            'lines.*.is_free' => 'boolean',
        ];
    }
}
