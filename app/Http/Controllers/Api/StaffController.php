<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $query = Staff::with('user');

        if ($request->has('type')) {
            $query->where('staff_type', $request->type);
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'staff_type' => 'required|in:employee,agent,driver,picker,manager',
            'salary_monthly' => 'numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $staff = Staff::create($request->all());

        return response()->json(['message' => 'Staff created', 'staff' => $staff], 201);
    }

    public function show(Staff $staff)
    {
        return response()->json($staff->load('user'));
    }

    public function update(Request $request, Staff $staff)
    {
        $request->validate([
            'staff_type' => 'sometimes|in:employee,agent,driver,picker,manager',
            'salary_monthly' => 'numeric|min:0',
            'is_active' => 'boolean'
        ]);

        $staff->update($request->all());

        return response()->json(['message' => 'Staff updated', 'staff' => $staff]);
    }
}
