<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Store extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'name',
        'phone',
        'currency',
    ];

    protected static function booted()
    {
        static::creating(function ($store) {
            if (empty($store->uuid)) {
                $store->uuid = (string) Str::uuid();
            }
        });
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'store_users')->withPivot('role')->withTimestamps();
    }
}
