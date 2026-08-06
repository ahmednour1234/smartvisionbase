<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixUserRoles extends Command
{
    protected $signature = 'fix:user-roles {--check-only : Only check, do not fix}';
    protected $description = 'Fix users without role_id or with invalid roles';

    public function handle(): int
    {
        $this->info('Checking and fixing user roles...');
        $this->newLine();

        $usersWithoutRole = User::whereNull('role_id')->orWhereNotIn('role_id', Role::pluck('id'))->get();
        
        if ($usersWithoutRole->isEmpty()) {
            $this->info('✓ All users have valid roles!');
            return 0;
        }

        $this->warn("Found {$usersWithoutRole->count()} user(s) without valid roles:");
        
        foreach ($usersWithoutRole as $user) {
            $this->line("  - {$user->email} (ID: {$user->id}, role_id: {$user->role_id})");
        }

        if ($this->option('check-only')) {
            return 0;
        }

        $this->newLine();
        $defaultRole = Role::where('slug', 'sales')->first();
        
        if (!$defaultRole) {
            $this->error('Sales role not found! Cannot assign default role.');
            return 1;
        }

        if (!$this->confirm('Assign sales role to users without roles?', true)) {
            return 0;
        }

        $fixed = 0;
        foreach ($usersWithoutRole as $user) {
            $user->role_id = $defaultRole->id;
            $user->save();
            $this->info("  ✓ Fixed: {$user->email}");
            $fixed++;
        }

        $this->newLine();
        $this->info("Fixed {$fixed} user(s)!");
        
        return 0;
    }
}
