<?php

namespace App\Models;

use App\Traits\MultiTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class DebtPayment extends Model
{
    use MultiTenant, SoftDeletes;

    protected $fillable = [
        'uuid',
        'store_id',
        'customer_id',
        'amount',
        'payment_date',
        'note',
    ];

    protected $casts = [
        'payment_date' => 'date',
    ];

    protected static function booted()
    {
        parent::booted();
        static::creating(function ($payment) {
            if (empty($payment->uuid)) {
                $payment->uuid = (string) Str::uuid();
            }
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
