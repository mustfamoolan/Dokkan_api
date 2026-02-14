<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class User extends Authenticatable implements FilamentUser, HasTenants
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->role === 'superadmin' && $this->is_active;
        }

        if ($panel->getId() === 'dashboard') {
            return in_array($this->role, ['owner', 'employee']) && $this->is_active;
        }

        return false;
    }

    public function getTenants(Panel $panel): Collection
    {
        if ($panel->getId() === 'dashboard') {
            return $this->stores;
        }

        return collect();
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->stores->contains($tenant);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'phone',
        'password',
        'auth_provider',
        'google_id',
        'role',
        'is_active',
        'photo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function stores()
    {
        return $this->belongsToMany(Store::class, 'store_users')->withPivot('role')->withTimestamps();
    }

    public function store()
    {
        return $this->hasOne(Store::class);
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }

    public function products()
    {
        return $this->hasManyThrough(Product::class, Store::class);
    }

    public function invoices()
    {
        return $this->hasManyThrough(Invoice::class, Store::class);
    }

    public function customers()
    {
        return $this->hasManyThrough(Customer::class, Store::class);
    }

    public function devices()
    {
        return $this->hasManyThrough(Device::class, Store::class);
    }
}
