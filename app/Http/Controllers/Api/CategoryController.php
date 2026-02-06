<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return CategoryResource::collection(ProductCategory::with('parent')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required_without:category_name|string|unique:product_categories,name',
            'category_name' => 'required_without:name|string|unique:product_categories,name',
            'parent_id' => 'nullable|exists:product_categories,id',
        ]);

        $data = $request->all();
        if (isset($data['category_name']) && !isset($data['name'])) {
            $data['name'] = $data['category_name'];
        }

        $category = ProductCategory::create($data);
        return new CategoryResource($category);
    }

    public function show(ProductCategory $category)
    {
        return new CategoryResource($category->load('parent', 'children'));
    }

    public function update(Request $request, ProductCategory $category)
    {
        $request->validate([
            'name' => 'required_without:category_name|string|unique:product_categories,name,' . $category->id,
            'category_name' => 'sometimes|string|unique:product_categories,name,' . $category->id,
            'parent_id' => 'nullable|exists:product_categories,id',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        if (isset($data['category_name']) && !isset($data['name'])) {
            $data['name'] = $data['category_name'];
        }

        $category->update($data);
        return new CategoryResource($category);
    }

    public function toggleStatus(ProductCategory $category)
    {
        $category->update(['is_active' => !$category->is_active]);
        return response()->json(['message' => 'تم تحديث حالة الصنف بنجاح', 'is_active' => $category->is_active]);
    }
}
