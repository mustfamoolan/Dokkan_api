<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Http\Resources\WarehouseResource;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index()
    {
        return WarehouseResource::collection(Warehouse::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $warehouse = Warehouse::create($validated);

        return new WarehouseResource($warehouse);
    }

    public function show(Warehouse $warehouse)
    {
        return new WarehouseResource($warehouse);
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'location' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $warehouse->update($validated);

        return new WarehouseResource($warehouse);
    }

    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Warehouse deleted successfully'
        ]);
    }

    public function toggleStatus(Warehouse $warehouse)
    {
        $warehouse->update(['is_active' => !$warehouse->is_active]);
        return response()->json([
            'status' => 'success',
            'message' => 'تم تغيير حالة المخزن بنجاح',
            'is_active' => $warehouse->is_active
        ]);
    }
}
