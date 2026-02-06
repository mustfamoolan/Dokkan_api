<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_base', 'is_active'];

    protected $casts = [
        'is_base' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function scopeBase($query)
    {
        return $query->where('is_base', true);
    }
}
