<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles;

    protected $table = 'users';
    public $timestamps = false;

    protected $guard_name = 'web';

    protected $fillable = [
        'name',
        'email',
        'password_hash',
        'role',
        'is_active',
        'must_change_password',
    ];

    protected $hidden = [
        'password_hash',
    ];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', 1);
    }

    public function meetings()
    {
        return $this->hasMany(Meeting::class, 'user_id');
    }

    public function isAdmin(): bool
    {
        // Backward-compatible admin check: prefer Spatie role if available.
        try {
            if (method_exists($this, 'hasRole') && $this->hasRole('admin')) {
                return true;
            }
        } catch (\Throwable $e) {
        }

        return $this->role === 'admin';
    }

    protected static function booted(): void
    {
        static::saved(function (self $user) {
            if (empty($user->role) || ! method_exists($user, 'syncRoles')) {
                return;
            }

            // Avoid fatal errors if roles are not seeded yet (e.g., first run).
            try {
                $user->syncRoles([$user->role]);
            } catch (\Throwable $e) {
            }
        });
    }
}
