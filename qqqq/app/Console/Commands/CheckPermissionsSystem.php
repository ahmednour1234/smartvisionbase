<?php

namespace App\Console\Commands;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckPermissionsSystem extends Command
{
    protected $signature = 'check:permissions-system {email?}';
    protected $description = 'Comprehensive check of users, roles, permissions, and role_permission relationships';

    public function handle(): int
    {
        $this->info('=== Comprehensive Permissions System Check ===');
        $this->newLine();

        $this->checkRoles();
        $this->newLine();
        $this->checkPermissions();
        $this->newLine();
        $this->checkRolePermissions();
        $this->newLine();
        $this->checkUsers();
        $this->newLine();

        $email = $this->argument('email');
        if ($email) {
            $this->checkSpecificUser($email);
        } else {
            $this->checkAllUsers();
        }

        return 0;
    }

    private function checkRoles(): void
    {
        $this->info('1. Checking Roles Table:');
        $roles = Role::all();
        
        if ($roles->isEmpty()) {
            $this->error('   No roles found!');
            return;
        }

        $this->table(
            ['ID', 'Name', 'Slug', 'Description'],
            $roles->map(fn($r) => [$r->id, $r->name, $r->slug, $r->description ?? '-'])->toArray()
        );
    }

    private function checkPermissions(): void
    {
        $this->info('2. Checking Permissions Table:');
        $permissions = Permission::all();
        
        if ($permissions->isEmpty()) {
            $this->error('   No permissions found!');
            return;
        }

        $this->info("   Total Permissions: {$permissions->count()}");
        
        $sample = $permissions->take(5);
        $this->table(
            ['ID', 'Name', 'Slug', 'Resource'],
            $sample->map(fn($p) => [$p->id, $p->name, $p->slug, $p->resource ?? '-'])->toArray()
        );

        if ($permissions->count() > 5) {
            $this->info("   ... and " . ($permissions->count() - 5) . " more");
        }
    }

    private function checkRolePermissions(): void
    {
        $this->info('3. Checking Role-Permission Relationships (role_permission table):');
        
        $rolePermissions = DB::table('role_permission')
            ->join('roles', 'role_permission.role_id', '=', 'roles.id')
            ->join('permissions', 'role_permission.permission_id', '=', 'permissions.id')
            ->select('roles.slug as role_slug', 'permissions.slug as permission_slug', 'permissions.id as permission_id')
            ->get()
            ->groupBy('role_slug');

        if ($rolePermissions->isEmpty()) {
            $this->error('   No role-permission relationships found!');
            return;
        }

        foreach ($rolePermissions as $roleSlug => $perms) {
            $this->info("   Role '{$roleSlug}': {$perms->count()} permissions");
            
            $permissionSlugs = $perms->pluck('permission_slug')->toArray();
            $sample = array_slice($permissionSlugs, 0, 5);
            $this->line("      Sample: " . implode(', ', $sample));
            if (count($permissionSlugs) > 5) {
                $this->line("      ... and " . (count($permissionSlugs) - 5) . " more");
            }
        }

        $this->newLine();
        $this->info('   Direct SQL Check (using permission_id and slug):');
        $directCheck = DB::table('role_permission as rp')
            ->join('permissions as p', 'rp.permission_id', '=', 'p.id')
            ->join('roles as r', 'rp.role_id', '=', 'r.id')
            ->where('r.slug', 'sales')
            ->where('p.slug', 'company.view')
            ->exists();
        
        $this->line("   Sales role has 'company.view' permission: " . ($directCheck ? 'YES ✓' : 'NO ✗'));
    }

    private function checkUsers(): void
    {
        $this->info('4. Checking Users Table:');
        $users = User::with('role')->get();
        
        if ($users->isEmpty()) {
            $this->error('   No users found!');
            return;
        }

        $this->info("   Total Users: {$users->count()}");
        
        $tableData = [];
        foreach ($users as $user) {
            $tableData[] = [
                $user->id,
                $user->email,
                $user->name,
                $user->role_id ?? 'NULL',
                $user->role?->slug ?? 'NO ROLE',
                $user->is_active ? 'YES' : 'NO',
            ];
        }

        $this->table(
            ['ID', 'Email', 'Name', 'role_id', 'Role Slug', 'Is Active'],
            $tableData
        );

        $usersWithoutRole = $users->filter(fn($u) => !$u->role_id || !$u->role);
        if ($usersWithoutRole->isNotEmpty()) {
            $this->warn("   ⚠️  {$usersWithoutRole->count()} user(s) without valid role_id or role!");
        }
    }

    private function checkSpecificUser(string $email): void
    {
        $this->info("5. Detailed Check for User: {$email}");
        $this->newLine();

        $user = User::where('email', $email)->with('role.permissions')->first();
        
        if (!$user) {
            $this->error("   User not found!");
            return;
        }

        $this->info("   User ID: {$user->id}");
        $this->info("   Name: {$user->name}");
        $this->info("   Email: {$user->email}");
        $this->info("   role_id: " . ($user->role_id ?? 'NULL'));
        $this->info("   Is Active: " . ($user->is_active ? 'YES' : 'NO'));

        if (!$user->role) {
            $this->error("   ⚠️  User has no role!");
            return;
        }

        $this->info("   Role: {$user->role->name} ({$user->role->slug})");
        $this->info("   Role Permissions Count: " . $user->role->permissions()->count());

        $this->newLine();
        $this->info('   Testing Key Permissions:');
        
        $testPermissions = [
            'company.view',
            'company.view.any',
            'company.create',
            'meeting.view',
            'meeting.view.any',
            'role.view',
            'role.view.any',
            'user.view',
            'user.view.any',
        ];

        foreach ($testPermissions as $perm) {
            $hasPermission = $user->hasPermission($perm);
            $status = $hasPermission ? '✓ YES' : '✗ NO';
            $this->line("      {$perm}: {$status}");
        }

        $this->newLine();
        $this->info('   All Role Permissions:');
        $permissions = $user->role->permissions;
        if ($permissions->isEmpty()) {
            $this->warn("      ⚠️  Role has no permissions assigned!");
        } else {
            $permissionList = $permissions->pluck('slug')->chunk(5);
            foreach ($permissionList as $chunk) {
                $this->line("      " . $chunk->implode(', '));
            }
        }

        $this->newLine();
        $this->info('   Direct Database Check (role_permission table):');
        $directPerms = DB::table('role_permission as rp')
            ->join('permissions as p', 'rp.permission_id', '=', 'p.id')
            ->where('rp.role_id', $user->role_id)
            ->select('p.id', 'p.slug', 'p.name')
            ->get();

        $this->info("      Found {$directPerms->count()} permissions via direct query");
        $sample = $directPerms->take(5);
        foreach ($sample as $perm) {
            $this->line("      ID:{$perm->id} - {$perm->slug} ({$perm->name})");
        }
    }

    private function checkAllUsers(): void
    {
        $this->info('5. Testing Permissions for All Users:');
        $this->newLine();

        $users = User::with('role.permissions')->whereNotNull('role_id')->get();
        
        if ($users->isEmpty()) {
            $this->warn('   No users with roles found!');
            return;
        }

        $tableData = [];
        foreach ($users as $user) {
            $hasCompanyView = $user->hasPermission('company.view');
            $hasCompanyViewAny = $user->hasPermission('company.view.any');
            $hasRoleView = $user->hasPermission('role.view');
            
            $tableData[] = [
                $user->email,
                $user->role?->slug ?? 'N/A',
                $hasCompanyView ? 'YES' : 'NO',
                $hasCompanyViewAny ? 'YES' : 'NO',
                $hasRoleView ? 'YES' : 'NO',
            ];
        }

        $this->table(
            ['Email', 'Role', 'company.view', 'company.view.any', 'role.view'],
            $tableData
        );
    }
}
