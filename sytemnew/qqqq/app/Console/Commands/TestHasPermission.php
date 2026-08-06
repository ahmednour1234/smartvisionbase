<?php

namespace App\Console\Commands;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;

class TestHasPermission extends Command
{
    protected $signature = 'test:has-permission';
    protected $description = 'Test the hasPermission function';

    public function handle(): int
    {
        $this->info('Testing hasPermission function...');
        $this->newLine();

        $this->testUserWithPermission();
        $this->testUserWithoutPermission();
        $this->testUserWithoutRole();
        $this->testInactiveUser();
        $this->testEmptyPermissionSlug();
        $this->testRoleHasPermission();
        $this->testRoleWithoutPermission();

        $this->newLine();
        $this->info('All tests completed!');
        return 0;
    }

    private function testUserWithPermission(): void
    {
        $this->info('Test 1: User with permission');
        
        $permission = Permission::firstOrCreate(
            ['slug' => 'test.permission'],
            ['name' => 'Test Permission']
        );

        $role = Role::firstOrCreate(
            ['slug' => 'test-role'],
            ['name' => 'Test Role']
        );

        if (!$role->permissions()->where('permissions.id', $permission->id)->exists()) {
            $role->permissions()->attach($permission->id);
        }

        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => 'password',
                'role_id' => $role->id,
                'is_active' => true,
            ]
        );

        $result = $user->hasPermission('test.permission');
        $this->line("  Result: " . ($result ? 'PASS' : 'FAIL'));
        $this->assert($result, 'User should have permission');
    }

    private function testUserWithoutPermission(): void
    {
        $this->info('Test 2: User without permission');
        
        $role = Role::firstOrCreate(
            ['slug' => 'test-role-2'],
            ['name' => 'Test Role 2']
        );

        $user = User::firstOrCreate(
            ['email' => 'test2@example.com'],
            [
                'name' => 'Test User 2',
                'password' => 'password',
                'role_id' => $role->id,
                'is_active' => true,
            ]
        );

        $result = $user->hasPermission('nonexistent.permission');
        $this->line("  Result: " . (!$result ? 'PASS' : 'FAIL'));
        $this->assert(!$result, 'User should not have permission');
    }

    private function testUserWithoutRole(): void
    {
        $this->info('Test 3: User without role');
        
        $user = User::firstOrCreate(
            ['email' => 'test3@example.com'],
            [
                'name' => 'Test User 3',
                'password' => 'password',
                'role_id' => null,
                'is_active' => true,
            ]
        );

        $result = $user->hasPermission('any.permission');
        $this->line("  Result: " . (!$result ? 'PASS' : 'FAIL'));
        $this->assert(!$result, 'User without role should not have permission');
    }

    private function testInactiveUser(): void
    {
        $this->info('Test 4: Inactive user');
        
        $permission = Permission::firstOrCreate(
            ['slug' => 'test.permission.2'],
            ['name' => 'Test Permission 2']
        );

        $role = Role::firstOrCreate(
            ['slug' => 'test-role-3'],
            ['name' => 'Test Role 3']
        );

        if (!$role->permissions()->where('permissions.id', $permission->id)->exists()) {
            $role->permissions()->attach($permission->id);
        }

        $user = User::firstOrCreate(
            ['email' => 'test4@example.com'],
            [
                'name' => 'Test User 4',
                'password' => 'password',
                'role_id' => $role->id,
                'is_active' => false,
            ]
        );

        $result = $user->hasPermission('test.permission.2');
        $this->line("  Result: " . (!$result ? 'PASS' : 'FAIL'));
        $this->assert(!$result, 'Inactive user should not have permission');
    }

    private function testEmptyPermissionSlug(): void
    {
        $this->info('Test 5: Empty permission slug');
        
        $role = Role::firstOrCreate(
            ['slug' => 'test-role-4'],
            ['name' => 'Test Role 4']
        );

        $user = User::firstOrCreate(
            ['email' => 'test5@example.com'],
            [
                'name' => 'Test User 5',
                'password' => 'password',
                'role_id' => $role->id,
                'is_active' => true,
            ]
        );

        $result = $user->hasPermission('');
        $this->line("  Result: " . (!$result ? 'PASS' : 'FAIL'));
        $this->assert(!$result, 'Empty permission slug should return false');
    }

    private function testRoleHasPermission(): void
    {
        $this->info('Test 6: Role has permission');
        
        $permission = Permission::firstOrCreate(
            ['slug' => 'test.permission.3'],
            ['name' => 'Test Permission 3']
        );

        $role = Role::firstOrCreate(
            ['slug' => 'test-role-5'],
            ['name' => 'Test Role 5']
        );

        if (!$role->permissions()->where('permissions.id', $permission->id)->exists()) {
            $role->permissions()->attach($permission->id);
        }

        $result = $role->hasPermission('test.permission.3');
        $this->line("  Result: " . ($result ? 'PASS' : 'FAIL'));
        $this->assert($result, 'Role should have permission');
    }

    private function testRoleWithoutPermission(): void
    {
        $this->info('Test 7: Role without permission');
        
        $role = Role::firstOrCreate(
            ['slug' => 'test-role-6'],
            ['name' => 'Test Role 6']
        );

        $result = $role->hasPermission('nonexistent.permission');
        $this->line("  Result: " . (!$result ? 'PASS' : 'FAIL'));
        $this->assert(!$result, 'Role should not have permission');
    }

    private function assert(bool $condition, string $message): void
    {
        if (!$condition) {
            $this->error("  FAILED: $message");
        }
    }
}
