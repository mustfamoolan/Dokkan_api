<?php

namespace App\Traits;

use App\Scopes\TenantScope;

trait MultiTenant
{
    protected static function booted()
    {
        static::addGlobalScope(new TenantScope());

        static::creating(function ($model) {
            if (app()->bound('current_store_id')) {
                $model->store_id = app('current_store_id');
            }
        });
    }

    public function store()
    {
        return $this->belongsTo(\App\Models\Store::class);
    }
}
