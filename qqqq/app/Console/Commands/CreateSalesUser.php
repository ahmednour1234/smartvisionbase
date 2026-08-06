<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateSalesUser extends Command
{
    protected $signature = 'create:sales-user {email=sales@test.com} {name=Sales User}';
    protected $description = 'Create a sales user for testing';

    public function handle(): int
    {
        $salesRole = Role::where('slug', 'sales')->first();
        
        if (!$salesRole) {
            $this->error('Sales role not found! Please run RoleSeeder first.');
            return 1;
        }

        $email = $this->argument('email');
        $name = $this->argument('name');

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make('password'),
                'role_id' => $salesRole->id,
                'is_active' => true,
            ]
        );

        $this->info("Sales user created/updated:");
        $this->info("Email: {$user->email}");
        $this->info("Password: password");
        $this->info("Role: {$salesRole->name}");
        $this->info("Permissions: " . $salesRole->permissions()->count());

        $this->info("\nTesting permissions:");
        $this->info("company.view: " . ($user->hasPermission('company.view') ? 'YES' : 'NO'));
        $this->info("company.view.any: " . ($user->hasPermission('company.view.any') ? 'YES' : 'NO'));
        $this->info("meeting.view: " . ($user->hasPermission('meeting.view') ? 'YES' : 'NO'));

        return 0;
    }
}
