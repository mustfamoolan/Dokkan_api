<?php

namespace App\Models;

use App\Traits\MultiTenant;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use MultiTenant, SoftDeletes, HasApiTokens, Notifiable;

    protected $fillable = [
        'uuid',
        'store_id',
        'name',
        'phone',
        'email',
        'password',
        'is_active',
        'address',
        'total_debt',
        'notes',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
            'total_debt' => 'decimal:2',
        ];
    }

    protected static function booted()
    {
        parent::booted();
        static::creating(function ($customer) {
            if (empty($customer->uuid)) {
                $customer->uuid = (string) Str::uuid();
            }
        });
    }
}
