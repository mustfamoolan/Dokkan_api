<?php

namespace App\Models;

use App\Traits\MultiTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use MultiTenant, SoftDeletes;

    protected $fillable = [
        'uuid',
        'store_id',
        'customer_id',
        'invoice_number',
        'total_amount',
        'discount',
        'paid_amount',
        'remaining_amount',
        'payment_type',
        'status',
    ];

    protected static function booted()
    {
        parent::booted();
        static::creating(function ($invoice) {
            if (empty($invoice->uuid)) {
                $invoice->uuid = (string) Str::uuid();
            }
        });
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
