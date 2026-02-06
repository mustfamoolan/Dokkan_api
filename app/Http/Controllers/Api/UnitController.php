<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        return response()->json(Unit::all());
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255', 'is_base' => 'boolean']);

        $unit = Unit::create($request->all());

        return response()->json(['message' => 'Created', 'unit' => $unit], 201);
    }
}
