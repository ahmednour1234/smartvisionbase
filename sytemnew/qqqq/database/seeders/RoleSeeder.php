<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    protected static array $roleDefinitions = [
        'admin' => [
            'name' => 'Admin',
            'description' => 'Full system access with all permissions',
            'resources' => [
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
        ],
        'manager' => [
            'name' => 'Manager',
            'description' => 'Management access with limited administrative permissions',
            'resources' => [
                'company' => ['view', 'view.any', 'create', 'update', 'update.any', 'delete', 'delete.any'],
                'country' => ['view', 'view.any', 'create', 'update', 'delete'],
                'event' => ['view', 'view.any', 'create', 'update', 'delete'],
                'package' => ['view', 'view.any', 'create', 'update', 'delete'],
                'meeting' => ['view', 'view.any', 'create', 'update', 'delete'],
                'jobrun' => ['view', 'view.any'],
            ],
        ],
        'sales' => [
            'name' => 'Sales',
            'description' => 'Sales team member with access to leads and meetings',
            'resources' => [
                'company' => ['view', 'view.any', 'create', 'update'],
                'meeting' => ['view', 'view.any', 'create', 'update'],
                'event' => ['view', 'view.any'],
                'package' => ['view', 'view.any'],
                'country' => ['view', 'view.any'],
            ],
        ],
    ];

    public function run(): void
    {
        foreach (self::$roleDefinitions as $slug => $roleData) {
            $permissions = $this->buildPermissionsFromResources($roleData['resources']);

            $role = Role::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $roleData['name'],
                    'description' => $roleData['description'],
                ]
            );

            $permissionIds = Permission::whereIn('slug', $permissions)->pluck('id');
            $role->permissions()->sync($permissionIds);

            $this->command->info("Role '{$roleData['name']}' created/updated with " . count($permissions) . " permissions.");
        }
    }

    protected function buildPermissionsFromResources(array $resources): array
    {
        $permissions = [];
        foreach ($resources as $resource => $actions) {
            foreach ($actions as $action) {
                $permissions[] = "{$resource}.{$action}";
            }
        }
        return $permissions;
    }
}
