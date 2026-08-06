<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoleResourceTest extends TestCase
{
    use RefreshDatabase;

    protected Role $adminRole;
    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->adminRole = Role::create([
            'name' => 'Admin',
            'slug' => 'admin',
            'description' => 'Administrator role',
        ]);

        $this->adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => 'password',
            'role_id' => $this->adminRole->id,
            'is_active' => true,
        ]);

        Permission::create(['name' => 'View Roles', 'slug' => 'role.view.any']);
        Permission::create(['name' => 'Create Role', 'slug' => 'role.create']);
        Permission::create(['name' => 'Update Role', 'slug' => 'role.update']);
        Permission::create(['name' => 'Delete Role', 'slug' => 'role.delete']);

        $this->adminRole->permissions()->sync(Permission::pluck('id'));
    }

    public function test_role_resource_can_be_accessed(): void
    {
        $this->actingAs($this->adminUser, 'web');
        
        $response = $this->get('/admin/roles');
        $response->assertStatus(200);
    }

    public function test_can_create_role(): void
    {
        $this->actingAs($this->adminUser, 'web');

        $roleData = [
            'name' => 'Test Role',
            'slug' => 'test-role',
            'description' => 'Test description',
        ];

        $role = Role::create($roleData);
        
        $this->assertDatabaseHas('roles', [
            'name' => 'Test Role',
            'slug' => 'test-role',
        ]);
    }

    public function test_can_update_role(): void
    {
        $this->actingAs($this->adminUser, 'web');

        $role = Role::create([
            'name' => 'Original Role',
            'slug' => 'original-role',
        ]);

        $role->update(['name' => 'Updated Role']);
        
        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'Updated Role',
        ]);
    }

    public function test_can_assign_permissions_to_role(): void
    {
        $this->actingAs($this->adminUser, 'web');

        $permission1 = Permission::create(['name' => 'Permission 1', 'slug' => 'permission.1']);
        $permission2 = Permission::create(['name' => 'Permission 2', 'slug' => 'permission.2']);

        $role = Role::create([
            'name' => 'Role with Permissions',
            'slug' => 'role-with-permissions',
        ]);

        $role->permissions()->sync([$permission1->id, $permission2->id]);

        $this->assertEquals(2, $role->permissions()->count());
        $this->assertTrue($role->hasPermission('permission.1'));
        $this->assertTrue($role->hasPermission('permission.2'));
    }

    public function test_role_slug_must_be_unique(): void
    {
        Role::create([
            'name' => 'First Role',
            'slug' => 'unique-slug',
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Role::create([
            'name' => 'Second Role',
            'slug' => 'unique-slug',
        ]);
    }
}
