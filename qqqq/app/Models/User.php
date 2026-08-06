<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            if (preg_match('/^\$2[ayb]\$/', $value) || preg_match('/^\$argon2/', $value)) {
                $this->attributes['password'] = $value;
            } else {
                $this->attributes['password'] = Hash::make($value);
            }
        }
    }

    public function meetings()
    {
        return $this->hasMany(Meeting::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function hasPermission(string $permissionSlug): bool
    {
        if (empty($permissionSlug)) {
            return false;
        }

        if (! $this->is_active) {
            return false;
        }

        if (! $this->role_id) {
            return false;
        }

        return DB::table('role_permission')
            ->join('permissions', 'role_permission.permission_id', '=', 'permissions.id')
            ->where('role_permission.role_id', $this->role_id)
            ->where('permissions.slug', $permissionSlug)
            ->exists();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        try {
            if (! $this->is_active) {
                return false;
            }

            if (! $this->role_id) {
                return false;
            }

            if (! $this->relationLoaded('role')) {
                $this->load('role');
            }

            if (! $this->role) {
                return false;
            }

            $roleSlug = $this->role->slug ?? null;

            if (! $roleSlug) {
                return false;
            }

            return match ($panel->getId()) {
                'admin' => in_array($roleSlug, ['admin', 'manager', 'sales'], true),
                'employee' => in_array($roleSlug, ['sales', 'admin', 'manager'], true),
                default => false,
            };
        } catch (\Exception $e) {
            return false;
        }
    }
}
