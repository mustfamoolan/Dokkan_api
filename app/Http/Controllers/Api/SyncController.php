<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SyncController extends Controller
{
    /**
     * Pull data from server that changed after last_sync_at
     */
    public function pull(Request $request)
    {
        $lastSyncAt = $request->query('last_sync_at', '1970-01-01 00:00:00');

        return response()->json([
            'products' => \App\Models\Product::withTrashed()->where('updated_at', '>', $lastSyncAt)->get(),
            'customers' => \App\Models\Customer::withTrashed()->where('updated_at', '>', $lastSyncAt)->get(),
            'invoices' => \App\Models\Invoice::withTrashed()->where('updated_at', '>', $lastSyncAt)->get(),
            'installment_plans' => \App\Models\InstallmentPlan::withTrashed()->where('updated_at', '>', $lastSyncAt)->get(),
            'cashbox_transactions' => \App\Models\CashboxTransaction::withTrashed()->where('updated_at', '>', $lastSyncAt)->get(),
            'expenses' => \App\Models\Expense::withTrashed()->where('updated_at', '>', $lastSyncAt)->get(),
            'server_time' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Push local changes to server
     */
    public function push(Request $request)
    {
        // To be implemented: complex logic for syncing changes from device to server
        return response()->json(['message' => 'Sync push partially implemented.']);
    }
}
