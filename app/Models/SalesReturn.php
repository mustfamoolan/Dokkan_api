<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_no',
        'sales_invoice_id',
        'return_date',
        'total_iqd',
        'status',
        'journal_entry_id',
        'created_by',
        'notes',
    ];

    public function invoice()
    {
        return $this->belongsTo(SalesInvoice::class, 'sales_invoice_id');
    }

    public function lines()
    {
        return $this->hasMany(SalesReturnLine::class);
    }
}
