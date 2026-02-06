<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentAllocation;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function store(Request $request)
    {
        $payment = Payment::create([
            'payment_no' => 'PY-' . time(),
            'party_id' => $request->party_id,
            'supplier_id' => $request->supplier_id,
            'expense_account_id' => $request->expense_account_id,
            'payment_type' => $request->payment_type,
            'amount_iqd' => $request->amount_iqd,
            'status' => 'draft',
            'created_by' => auth()->id(),
            'notes' => $request->notes,
        ]);

        return response()->json(['message' => 'Payment created', 'payment' => $payment], 201);
    }

    public function allocate(Request $request, Payment $payment)
    {
        if ($payment->status !== 'draft')
            abort(400, 'Must be draft');

        PaymentAllocation::create([
            'payment_id' => $payment->id,
            'purchase_invoice_id' => $request->purchase_invoice_id,
            'allocated_iqd' => $request->allocated_iqd,
        ]);

        return response()->json(['message' => 'Allocated']);
    }

    public function post(Payment $payment)
    {
        if ($payment->status !== 'draft')
            abort(400, 'Invalid status');
        $payment->update(['status' => 'posted']);
        return response()->json(['message' => 'Payment posted']);
    }

    public function show(Payment $payment)
    {
        return response()->json($payment->load('allocations.invoice'));
    }
}
