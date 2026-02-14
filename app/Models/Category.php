<?php

namespace App\Models;

use App\Traits\MultiTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Category extends Model
{
    use MultiTenant, SoftDeletes;

    protected $fillable = [
        'uuid',
        'store_id',
        'name',
        'icon',
        'image_path',
    ];

    protected static function booted()
    {
        parent::booted();
        static::creating(function ($category) {
            if (empty($category->uuid)) {
                $category->uuid = (string) Str::uuid();
            }
        });
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
