<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CashAccount;
use Illuminate\Http\Request;

class CashAccountController extends Controller
{
    public function index()
    {
        return response()->json(CashAccount::where('is_active', true)->get());
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required', 'account_id' => 'required|exists:accounts,id']);
        $account = CashAccount::create($request->all());
        return response()->json($account, 201);
    }

    public function update(Request $request, CashAccount $cashAccount)
    {
        $cashAccount->update($request->all());
        return response()->json($cashAccount);
    }
}
