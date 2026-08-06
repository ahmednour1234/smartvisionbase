<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;

class CheckSalesPermissions extends Command
{
    protected $signature = 'check:sales-permissions';
    protected $description = 'Check sales user permissions';

    public function handle(): int
    {
        $salesRole = Role::where('slug', 'sales')->first();
        
        if (!$salesRole) {
            $this->error('Sales role not found!');
            return 1;
        }

        $this->info("Sales Role ID: {$salesRole->id}");
        $this->info("Sales Role Permissions Count: " . $salesRole->permissions()->count());
        
        $permissions = $salesRole->permissions()->pluck('slug')->toArray();
        $this->info("Permissions: " . implode(', ', $permissions));
        
        $salesUser = User::whereHas('role', fn($q) => $q->where('slug', 'sales'))->first();
        
        if (!$salesUser) {
            $this->warn('No sales user found!');
            return 0;
        }

        $this->info("\nSales User: {$salesUser->email}");
        $this->info("Role ID: {$salesUser->role_id}");
        $this->info("Is Active: " . ($salesUser->is_active ? 'YES' : 'NO'));
        
        $this->info("\nPermission Checks:");
        $this->info("company.view: " . ($salesUser->hasPermission('company.view') ? 'YES' : 'NO'));
        $this->info("company.view.any: " . ($salesUser->hasPermission('company.view.any') ? 'YES' : 'NO'));
        $this->info("company.create: " . ($salesUser->hasPermission('company.create') ? 'YES' : 'NO'));
        $this->info("meeting.view: " . ($salesUser->hasPermission('meeting.view') ? 'YES' : 'NO'));
        $this->info("event.view: " . ($salesUser->hasPermission('event.view') ? 'YES' : 'NO'));

        return 0;
    }
}
