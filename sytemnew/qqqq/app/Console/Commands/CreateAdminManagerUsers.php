<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminManagerUsers extends Command
{
    protected $signature = 'create:admin-manager-users';
    protected $description = 'Create admin and manager users for testing';

    public function handle(): int
    {
        $adminRole = Role::where('slug', 'admin')->first();
        $managerRole = Role::where('slug', 'manager')->first();
        
        if (!$adminRole) {
            $this->error('Admin role not found! Please run RoleSeeder first.');
            return 1;
        }

        if (!$managerRole) {
            $this->error('Manager role not found! Please run RoleSeeder first.');
            return 1;
        }

        $admin = User::updateOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
                'is_active' => true,
            ]
        );

        $manager = User::updateOrCreate(
            ['email' => 'manager@test.com'],
            [
                'name' => 'Manager User',
                'password' => Hash::make('password'),
                'role_id' => $managerRole->id,
                'is_active' => true,
            ]
        );

        $this->info("Admin user created/updated:");
        $this->info("Email: {$admin->email}");
        $this->info("Password: password");
        $this->info("Role: {$adminRole->name}");
        $this->info("Permissions: " . $adminRole->permissions()->count());

        $this->info("\nManager user created/updated:");
        $this->info("Email: {$manager->email}");
        $this->info("Password: password");
        $this->info("Role: {$managerRole->name}");
        $this->info("Permissions: " . $managerRole->permissions()->count());

        $this->info("\nTesting Admin permissions:");
        $this->info("role.view: " . ($admin->hasPermission('role.view') ? 'YES' : 'NO'));
        $this->info("role.view.any: " . ($admin->hasPermission('role.view.any') ? 'YES' : 'NO'));
        $this->info("user.view: " . ($admin->hasPermission('user.view') ? 'YES' : 'NO'));
        $this->info("permission.view: " . ($admin->hasPermission('permission.view') ? 'YES' : 'NO'));

        $this->info("\nTesting Manager permissions:");
        $this->info("company.view: " . ($manager->hasPermission('company.view') ? 'YES' : 'NO'));
        $this->info("company.view.any: " . ($manager->hasPermission('company.view.any') ? 'YES' : 'NO'));
        $this->info("role.view: " . ($manager->hasPermission('role.view') ? 'YES' : 'NO'));
        $this->info("user.view: " . ($manager->hasPermission('user.view') ? 'YES' : 'NO'));

        return 0;
    }
}
