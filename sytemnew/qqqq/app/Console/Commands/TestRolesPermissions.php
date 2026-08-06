<?php

namespace App\Console\Commands;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestRolesPermissions extends Command
{
    protected $signature = 'test:roles-permissions {--user-id=}';
    protected $description = 'Test roles and permissions across all modules';

    protected array $resources = [
        'permission' => ['view.any', 'create', 'update', 'delete'],
        'role' => ['view.any', 'create', 'update', 'delete'],
        'user' => ['view.any', 'create', 'update', 'delete'],
        'company' => ['view.any', 'create', 'update', 'update.any', 'delete', 'delete.any'],
        'country' => ['view.any', 'create', 'update', 'delete'],
        'event' => ['view.any', 'create', 'update', 'delete'],
        'package' => ['view.any', 'create', 'update', 'delete'],
        'meeting' => ['view.any', 'create', 'update', 'delete'],
        'jobrun' => ['view.any'],
    ];

    public function handle()
    {
        $this->info('=== Testing Roles and Permissions ===');
        $this->newLine();

        $this->testPermissionsExist();
        $this->newLine();
        $this->testRolesHavePermissions();
        $this->newLine();
        $this->testUsersCanAccess();
        $this->newLine();
        $this->findMissingPermissions();
    }

    protected function testPermissionsExist()
    {
        $this->info('1. Checking if all required permissions exist in database...');
        $this->newLine();

        $allPermissions = Permission::pluck('slug')->toArray();
        $missing = [];
        $found = [];

        foreach ($this->resources as $resource => $actions) {
            foreach ($actions as $action) {
                $slug = "{$resource}.{$action}";
                if (in_array($slug, $allPermissions)) {
                    $found[] = $slug;
                } else {
                    $missing[] = $slug;
                    $this->error("  ❌ Missing: {$slug}");
                }
            }
        }

        if (empty($missing)) {
            $this->info("  ✅ All " . count($found) . " required permissions exist!");
        } else {
            $this->warn("  ⚠️  Found " . count($found) . " permissions, missing " . count($missing));
        }
    }

    protected function testRolesHavePermissions()
    {
        $this->info('2. Checking roles have correct permissions...');
        $this->newLine();

        $expectedPermissions = [
            'admin' => [
                'permission' => ['view', 'view.any', 'create', 'update', 'delete'],
                'role' => ['view', 'view.any', 'create', 'update', 'delete'],
                'user' => ['view', 'view.any', 'create', 'update', 'delete'],
                'company' => ['view', 'view.any', 'create', 'update', 'update.any', 'delete', 'delete.any'],
                'country' => ['view', 'view.any', 'create', 'update', 'delete'],
                'event' => ['view', 'view.any', 'create', 'update', 'delete'],
                'package' => ['view', 'view.any', 'create', 'update', 'delete'],
                'meeting' => ['view', 'view.any', 'create', 'update', 'delete'],
                'jobrun' => ['view', 'view.any'],
            ],
            'manager' => [
                'company' => ['view', 'view.any', 'create', 'update', 'update.any', 'delete', 'delete.any'],
                'country' => ['view', 'view.any', 'create', 'update', 'delete'],
                'event' => ['view', 'view.any', 'create', 'update', 'delete'],
                'package' => ['view', 'view.any', 'create', 'update', 'delete'],
                'meeting' => ['view', 'view.any', 'create', 'update', 'delete'],
                'jobrun' => ['view', 'view.any'],
            ],
            'sales' => [
                'company' => ['view', 'view.any', 'create', 'update'],
                'meeting' => ['view', 'view.any', 'create', 'update'],
                'event' => ['view', 'view.any'],
                'package' => ['view', 'view.any'],
                'country' => ['view', 'view.any'],
            ],
        ];

        $roles = Role::with('permissions')->get();

        foreach ($roles as $role) {
            $this->info("  Role: {$role->name} ({$role->slug})");
            $rolePermissions = $role->permissions->pluck('slug')->toArray();
            $count = count($rolePermissions);
            $this->line("    Permissions: {$count}");

            if (!isset($expectedPermissions[$role->slug])) {
                $this->warn("    ⚠️  No expected permissions defined for this role");
                continue;
            }

            $expected = [];
            foreach ($expectedPermissions[$role->slug] as $resource => $actions) {
                foreach ($actions as $action) {
                    $expected[] = "{$resource}.{$action}";
                }
            }

            $missing = array_diff($expected, $rolePermissions);
            $extra = array_diff($rolePermissions, $expected);

            if (!empty($missing)) {
                $this->error("    ❌ Missing " . count($missing) . " expected permissions:");
                foreach (array_slice($missing, 0, 10) as $perm) {
                    $this->line("      - {$perm}");
                }
                if (count($missing) > 10) {
                    $this->line("      ... and " . (count($missing) - 10) . " more");
                }
            }

            if (!empty($extra)) {
                $this->warn("    ⚠️  Has " . count($extra) . " extra permissions (not in expected list)");
            }

            if (empty($missing) && empty($extra)) {
                $this->info("    ✅ Has exactly the expected permissions");
            } elseif (empty($missing)) {
                $this->info("    ✅ Has all expected permissions (plus some extra)");
            }
            $this->newLine();
        }
    }

    protected function testUsersCanAccess()
    {
        $this->info('3. Testing user access to resources...');
        $this->newLine();

        $userId = $this->option('user-id');
        $users = $userId ? User::where('id', $userId)->get() : User::where('is_active', true)->limit(5)->get();

        if ($users->isEmpty()) {
            $this->warn('  No active users found to test.');
            return;
        }

        foreach ($users as $user) {
            $this->info("  User: {$user->name} ({$user->email})");
            
            if (!$user->role_id) {
                $this->error("    ❌ No role_id assigned!");
                continue;
            }

            if (!$user->role) {
                $this->error("    ❌ Role not found (role_id: {$user->role_id})!");
                continue;
            }

            $this->line("    Role: {$user->role->name} ({$user->role->slug})");

            $canAccess = [];
            $cannotAccess = [];

            foreach ($this->resources as $resource => $actions) {
                $viewAnySlug = "{$resource}.view.any";
                $hasViewAny = $user->hasPermission($viewAnySlug);
                
                if ($hasViewAny) {
                    $canAccess[] = $resource;
                } else {
                    $cannotAccess[] = $resource;
                }
            }

            $this->info("    ✅ Can access: " . implode(', ', $canAccess));
            if (!empty($cannotAccess)) {
                $this->warn("    ❌ Cannot access: " . implode(', ', $cannotAccess));
            }
            $this->newLine();
        }
    }

    protected function findMissingPermissions()
    {
        $this->info('4. Finding permission problems...');
        $this->newLine();

        $problems = [];

        $allPermissions = Permission::pluck('slug')->toArray();
        $roles = Role::with('permissions')->get();

        foreach ($roles as $role) {
            $rolePermissions = $role->permissions->pluck('slug')->toArray();
            
            foreach ($this->resources as $resource => $actions) {
                foreach ($actions as $action) {
                    $slug = "{$resource}.{$action}";
                    
                    if (!in_array($slug, $allPermissions)) {
                        $problems[] = [
                            'type' => 'missing_permission',
                            'role' => $role->name,
                            'resource' => $resource,
                            'action' => $action,
                            'message' => "Permission '{$slug}' does not exist in database",
                        ];
                    }
                }
            }
        }

        $usersWithoutRole = User::whereNull('role_id')->orWhereNotIn('role_id', $roles->pluck('id'))->count();
        if ($usersWithoutRole > 0) {
            $problems[] = [
                'type' => 'users_without_role',
                'count' => $usersWithoutRole,
                'message' => "{$usersWithoutRole} users have no valid role assigned",
            ];
        }

        if (empty($problems)) {
            $this->info("  ✅ No problems found!");
        } else {
            $this->warn("  ⚠️  Found " . count($problems) . " problems:");
            foreach ($problems as $problem) {
                $this->line("    - {$problem['message']}");
            }
        }
    }
}
