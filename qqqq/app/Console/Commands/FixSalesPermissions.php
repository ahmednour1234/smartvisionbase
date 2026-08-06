<?php

namespace App\Console\Commands;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Console\Command;

class FixSalesPermissions extends Command
{
    protected $signature = 'fix:sales-permissions';
    protected $description = 'Fix Sales role permissions by running RoleSeeder';

    public function handle(): int
    {
        $this->info('Fixing all role permissions...');
        $this->newLine();

        $this->info('Step 1: Running PermissionSeeder...');
        $this->call('db:seed', ['--class' => 'PermissionSeeder']);
        $this->newLine();

        $this->info('Step 2: Running RoleSeeder...');
        $this->call('db:seed', ['--class' => 'RoleSeeder']);
        $this->newLine();

        $salesRole = Role::where('slug', 'sales')->first();
        $adminRole = Role::where('slug', 'admin')->first();
        $managerRole = Role::where('slug', 'manager')->first();

        $this->info('Step 3: Verifying permissions...');
        $this->newLine();

        if ($adminRole) {
            $this->info("Admin role: {$adminRole->permissions()->count()} permissions");
        }

        if ($managerRole) {
            $this->info("Manager role: {$managerRole->permissions()->count()} permissions");
        }

        if ($salesRole) {
            $count = $salesRole->permissions()->count();
            $this->info("Sales role: {$count} permissions");
            
            if ($count === 0) {
                $this->error('✗ Sales role has no permissions!');
                $this->newLine();
                $this->info('Manually assigning Sales permissions...');
                
                $salesPermissions = [
                    'company.view', 'company.view.any', 'company.create', 'company.update',
                    'meeting.view', 'meeting.view.any', 'meeting.create', 'meeting.update',
                    'event.view', 'event.view.any',
                    'package.view', 'package.view.any',
                    'country.view', 'country.view.any',
                ];

                $permissionIds = Permission::whereIn('slug', $salesPermissions)->pluck('id');
                
                if ($permissionIds->isEmpty()) {
                    $this->error('No permissions found in database! Run PermissionSeeder first.');
                    return 1;
                }

                $salesRole->permissions()->sync($permissionIds);
                $this->info("✓ Assigned {$permissionIds->count()} permissions to Sales role");
            } else {
                $this->info('✓ Sales role permissions OK');
                $this->newLine();
                $this->info('Sales role permissions:');
                $permissions = $salesRole->permissions()->pluck('slug')->toArray();
                $this->line('  ' . implode(', ', $permissions));
            }
        } else {
            $this->error('Sales role not found!');
            return 1;
        }

        return 0;
    }
}
