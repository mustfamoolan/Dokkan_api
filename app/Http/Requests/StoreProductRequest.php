<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|unique:products,sku',
            'category_id' => 'required|exists:product_categories,id',
            'purchase_price' => 'nullable|numeric|min:0',
            'sale_price_retail' => 'nullable|numeric|min:0',
            'sale_price_wholesale' => 'nullable|numeric|min:0',
            'base_unit_id' => 'required|exists:units,id',
            'has_pack' => 'boolean',
            // Pack fields required if has_pack is true
            'pack_unit_id' => 'required_if:has_pack,true|nullable|exists:units,id',
            'units_per_pack' => 'required_if:has_pack,true|nullable|numeric|min:1',
            'is_active' => 'boolean',
        ];
    }
}
