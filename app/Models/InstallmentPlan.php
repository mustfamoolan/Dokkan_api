<?php

namespace App\Models;

use App\Traits\MultiTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class InstallmentPlan extends Model
{
    use MultiTenant, SoftDeletes;

    protected $fillable = [
        'uuid',
        'store_id',
        'invoice_id',
        'customer_id',
        'total_amount',
        'down_payment',
        'remaining_amount',
        'installment_count',
        'installment_value',
        'start_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
    ];

    protected static function booted()
    {
        parent::booted();
        static::creating(function ($plan) {
            if (empty($plan->uuid)) {
                $plan->uuid = (string) Str::uuid();
            }
        });
    }

    public function payments()
    {
        return $this->hasMany(InstallmentPayment::class);
    }
}
