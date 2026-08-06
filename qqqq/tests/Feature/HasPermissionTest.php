<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HasPermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_permission_returns_true(): void
    {
        $permission = Permission::create([
            'name' => 'Test Permission',
            'slug' => 'test.permission',
        ]);

        $role = Role::create([
            'name' => 'Test Role',
            'slug' => 'test-role',
        ]);

        $role->permissions()->attach($permission->id);

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'role_id' => $role->id,
            'is_active' => true,
        ]);

        $this->assertTrue($user->hasPermission('test.permission'));
    }

    public function test_user_without_permission_returns_false(): void
    {
        $role = Role::create([
            'name' => 'Test Role',
            'slug' => 'test-role',
        ]);

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'role_id' => $role->id,
            'is_active' => true,
        ]);

        $this->assertFalse($user->hasPermission('nonexistent.permission'));
    }

    public function test_user_without_role_returns_false(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'role_id' => null,
            'is_active' => true,
        ]);

        $this->assertFalse($user->hasPermission('any.permission'));
    }

    public function test_inactive_user_returns_false(): void
    {
        $permission = Permission::create([
            'name' => 'Test Permission',
            'slug' => 'test.permission',
        ]);

        $role = Role::create([
            'name' => 'Test Role',
            'slug' => 'test-role',
        ]);

        $role->permissions()->attach($permission->id);

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'role_id' => $role->id,
            'is_active' => false,
        ]);

        $this->assertFalse($user->hasPermission('test.permission'));
    }

    public function test_empty_permission_slug_returns_false(): void
    {
        $role = Role::create([
            'name' => 'Test Role',
            'slug' => 'test-role',
        ]);

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'role_id' => $role->id,
            'is_active' => true,
        ]);

        $this->assertFalse($user->hasPermission(''));
    }

    public function test_role_has_permission_returns_true(): void
    {
        $permission = Permission::create([
            'name' => 'Test Permission',
            'slug' => 'test.permission',
        ]);

        $role = Role::create([
            'name' => 'Test Role',
            'slug' => 'test-role',
        ]);

        $role->permissions()->attach($permission->id);

        $this->assertTrue($role->hasPermission('test.permission'));
    }

    public function test_role_without_permission_returns_false(): void
    {
        $role = Role::create([
            'name' => 'Test Role',
            'slug' => 'test-role',
        ]);

        $this->assertFalse($role->hasPermission('nonexistent.permission'));
    }

    public function test_role_empty_permission_slug_returns_false(): void
    {
        $role = Role::create([
            'name' => 'Test Role',
            'slug' => 'test-role',
        ]);

        $this->assertFalse($role->hasPermission(''));
    }

    public function test_user_with_multiple_permissions(): void
    {
        $permission1 = Permission::create(['name' => 'Permission 1', 'slug' => 'permission.1']);
        $permission2 = Permission::create(['name' => 'Permission 2', 'slug' => 'permission.2']);
        $permission3 = Permission::create(['name' => 'Permission 3', 'slug' => 'permission.3']);

        $role = Role::create([
            'name' => 'Test Role',
            'slug' => 'test-role',
        ]);

        $role->permissions()->attach([$permission1->id, $permission2->id]);

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'role_id' => $role->id,
            'is_active' => true,
        ]);

        $this->assertTrue($user->hasPermission('permission.1'));
        $this->assertTrue($user->hasPermission('permission.2'));
        $this->assertFalse($user->hasPermission('permission.3'));
    }
}
