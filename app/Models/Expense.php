<?php

namespace App\Models;

use App\Traits\MultiTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Expense extends Model
{
    use MultiTenant, SoftDeletes;

    protected $fillable = [
        'uuid',
        'store_id',
        'amount',
        'category',
        'date',
        'note',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    protected static function booted()
    {
        parent::booted();
        static::creating(function ($expense) {
            if (empty($expense->uuid)) {
                $expense->uuid = (string) Str::uuid();
            }
        });
    }
}
