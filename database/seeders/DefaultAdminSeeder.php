<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DefaultAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('SV_DEFAULT_ADMIN_EMAIL', 'admin@smartvision.local');
        $password = env('SV_DEFAULT_ADMIN_PASSWORD', 'Admin@12345');

        $user = User::query()->where('email', $email)->first();

        if (! $user) {
            $user = User::create([
                'name' => 'Smart Vision Admin',
                'email' => $email,
                'password_hash' => Hash::make($password),
                'role' => 'admin',
                'is_active' => 1,
                'must_change_password' => 1,
            ]);
        } else {
            if ($user->role !== 'admin') {
                $user->role = 'admin';
                $user->save();
            }
        }

        // Keep spatie roles aligned
        if (method_exists($user, 'syncRoles')) {
            $user->syncRoles(['admin']);
        }
    }
}
