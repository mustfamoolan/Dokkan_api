<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptAllocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'receipt_id',
        'sales_invoice_id',
        'allocated_iqd',
    ];

    public function invoice()
    {
        return $this->belongsTo(SalesInvoice::class, 'sales_invoice_id');
    }
}
