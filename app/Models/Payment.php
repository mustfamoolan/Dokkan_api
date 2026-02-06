<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_no',
        'cash_account_id',
        'party_id',
        'supplier_id',
        'staff_id',
        'payment_type', // supplier_payment, expense, salary_payment, advance
        'expense_account_id',
        'amount_iqd',
        'status',
        'journal_entry_id',
        'created_by',
        'notes',
    ];

    public function cashAccount()
    {
        return $this->belongsTo(CashAccount::class);
    }

    public function allocations()
    {
        return $this->hasMany(PaymentAllocation::class);
    }
}
