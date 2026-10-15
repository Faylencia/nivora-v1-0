<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'phone', 'status'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')
            ->withPivot(['business_id']);
    }

    public function businesses(): BelongsToMany
    {
        return $this->belongsToMany(Business::class, 'user_roles')
            ->withPivot(['role_id']);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function memberSales(): HasMany
    {
        return $this->hasMany(Sale::class, 'member_user_id');
    }

    public function aiActions(): HasMany
    {
        return $this->hasMany(AiAction::class);
    }

    public function storeAnalyses(): HasMany
    {
        return $this->hasMany(StoreAnalysis::class);
    }

    public function hasRole(string|array $roles, ?int $businessId = null): bool
    {
        $roles = (array) $roles;

        if (in_array('super_admin', $roles, true) && $this->isSuperAdmin()) {
            return true;
        }

        if ($this->isSuperAdmin()) {
            return true;
        }

        if (in_array('member', $roles, true) && $this->roles()->where('roles.name', 'member')->wherePivotNull('business_id')->exists()) {
            return true;
        }

        $query = $this->roles()->whereIn('roles.name', $roles);

        if ($businessId !== null) {
            $query->wherePivot('business_id', $businessId);
        }

        return $query->exists();
    }

    public function isSuperAdmin(): bool
    {
        return $this->roles()->where('roles.name', 'super_admin')->exists();
    }
}
