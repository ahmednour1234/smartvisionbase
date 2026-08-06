<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;

class AuthTokenLoginTest extends TestCase
{
    public function test_token_login_returns_token_for_valid_admin(): void
    {
        $email = env('SV_DEFAULT_ADMIN_EMAIL', 'admin@smartvision.local');
        $password = env('SV_DEFAULT_ADMIN_PASSWORD', 'Admin@12345');

        // Ensure admin exists (Seeder creates it; this is defensive)
        User::query()->firstOrCreate([
            'email' => $email,
        ], [
            'name' => 'Smart Vision Admin',
            'password_hash' => Hash::make($password),
            'role' => 'admin',
            'is_active' => 1,
            'must_change_password' => 1,
        ]);

        $res = $this->postJson('/api/auth/login', [
            'email' => $email,
            'password' => $password,
        ]);

        $res->assertOk();
        $res->assertJsonStructure([
            'token',
            'user' => ['id', 'name', 'email', 'role'],
        ]);
    }

    public function test_token_login_rejects_invalid_credentials(): void
    {
        $res = $this->postJson('/api/auth/login', [
            'email' => 'nope@example.com',
            'password' => 'wrong',
        ]);

        $res->assertStatus(401);
    }
}
