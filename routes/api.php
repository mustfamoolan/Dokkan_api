<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public Routes
Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/register', [AuthController::class, 'register']); // Optional public register

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/me', [AuthController::class, 'me']);

    // Users Management
    Route::apiResource('users', UserController::class);
    Route::patch('users/{user}/status', [UserController::class, 'toggleStatus']);
    Route::patch('users/{user}/password', [UserController::class, 'changePassword']);

    // Accounting
    Route::apiResource('accounts', \App\Http\Controllers\Api\AccountController::class);

    Route::get('journal-entries', [\App\Http\Controllers\Api\JournalEntryController::class, 'index']);
    Route::get('journal-entries/{journalEntry}', [\App\Http\Controllers\Api\JournalEntryController::class, 'show']);
    Route::post('journal-entries/manual', [\App\Http\Controllers\Api\JournalEntryController::class, 'storeManual']);
    Route::post('journal-entries/{journalEntry}/post', [\App\Http\Controllers\Api\JournalEntryController::class, 'post']);
    Route::post('journal-entries/{journalEntry}/cancel', [\App\Http\Controllers\Api\JournalEntryController::class, 'cancel']);

    // Inventory & Products
    Route::apiResource('categories', \App\Http\Controllers\Api\CategoryController::class);
    Route::apiResource('units', \App\Http\Controllers\Api\UnitController::class);
    Route::apiResource('products', \App\Http\Controllers\Api\ProductController::class);
    Route::post('products/{product}/suppliers', [\App\Http\Controllers\Api\ProductController::class, 'syncSuppliers']);

    Route::get('inventory/balances', [\App\Http\Controllers\Api\InventoryController::class, 'balances']);
    Route::get('inventory/transactions', [\App\Http\Controllers\Api\InventoryController::class, 'transactions']);
    Route::post('inventory/opening-balance', [\App\Http\Controllers\Api\InventoryController::class, 'openingBalance']);

    // Purchases
    Route::apiResource('purchase-invoices', \App\Http\Controllers\Api\PurchaseInvoiceController::class);
    Route::post('purchase-invoices/{purchaseInvoice}/approve', [\App\Http\Controllers\Api\PurchaseInvoiceController::class, 'approve']);
    Route::post('purchase-invoices/{purchaseInvoice}/post', [\App\Http\Controllers\Api\PurchaseInvoiceController::class, 'post']);

    Route::post('purchase-returns', [\App\Http\Controllers\Api\PurchaseReturnController::class, 'store']);
    Route::post('purchase-returns/{purchaseReturn}/post', [\App\Http\Controllers\Api\PurchaseReturnController::class, 'post']);

    // Pre-Stage 6 (Staff & Workflow)
    Route::apiResource('staff', \App\Http\Controllers\Api\StaffController::class);
    Route::get('customers/{customer}/addresses', [\App\Http\Controllers\Api\CustomerAddressController::class, 'index']);
    Route::post('customers/{customer}/addresses', [\App\Http\Controllers\Api\CustomerAddressController::class, 'store']);
    Route::put('customers/{customer}/addresses/{address}', [\App\Http\Controllers\Api\CustomerAddressController::class, 'update']);
    Route::delete('customers/{customer}/addresses/{address}', [\App\Http\Controllers\Api\CustomerAddressController::class, 'destroy']);
});