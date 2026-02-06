<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Receipt;
use App\Models\ReceiptAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReceiptController extends Controller
{
    public function store(Request $request)
    {
        // Validation...
        $receipt = Receipt::create([
            'receipt_no' => 'RC-' . time(),
            'party_id' => $request->party_id,
            'customer_id' => $request->customer_id,
            'receipt_type' => $request->receipt_type,
            'amount_iqd' => $request->amount_iqd,
            'status' => 'draft',
            'created_by' => auth()->id(),
            'notes' => $request->notes,
        ]);

        return response()->json(['message' => 'Receipt created', 'receipt' => $receipt], 201);
    }

    public function allocate(Request $request, Receipt $receipt)
    {
        if ($receipt->status !== 'draft')
            abort(400, 'Must be draft');

        // Add allocation logic (validate total doesn't exceed receipt amount)
        ReceiptAllocation::create([
            'receipt_id' => $receipt->id,
            'sales_invoice_id' => $request->sales_invoice_id,
            'allocated_iqd' => $request->allocated_iqd,
        ]);

        return response()->json(['message' => 'Allocated']);
    }

    public function post(Receipt $receipt)
    {
        if ($receipt->status !== 'draft')
            abort(400, 'Invalid status');
        $receipt->update(['status' => 'posted']);
        // Observer handles Logic
        return response()->json(['message' => 'Receipt posted']);
    }

    public function show(Receipt $receipt)
    {
        return response()->json($receipt->load('allocations.invoice'));
    }
}
