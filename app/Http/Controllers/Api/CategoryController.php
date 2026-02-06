<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return response()->json(ProductCategory::all());
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255', 'parent_id' => 'nullable|exists:product_categories,id']);

        $category = ProductCategory::create($request->all());

        return response()->json(['message' => 'Created', 'category' => $category], 201);
    }
}
