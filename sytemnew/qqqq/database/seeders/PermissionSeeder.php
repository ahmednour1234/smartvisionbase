<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    protected static array $permissionDefinitions = [
        'permission' => [
            'view' => 'View Permissions',
            'view.any' => 'View Any Permission',
            'create' => 'Create Permission',
            'update' => 'Update Permission',
            'delete' => 'Delete Permission',
        ],
        'role' => [
            'view' => 'View Roles',
            'view.any' => 'View Any Role',
            'create' => 'Create Role',
            'update' => 'Update Role',
            'delete' => 'Delete Role',
        ],
        'user' => [
            'view' => 'View Users',
            'view.any' => 'View Any User',
            'create' => 'Create User',
            'update' => 'Update User',
            'delete' => 'Delete User',
        ],
        'company' => [
            'view' => 'View Companies',
            'view.any' => 'View Company',
            'create' => 'Create Company',
            'update' => 'Update Company',
            'update.any' => 'Update Any Company',
            'delete' => 'Delete Company',
            'delete.any' => 'Delete Any Company',
        ],
        'country' => [
            'view' => 'View Countries',
            'view.any' => 'View Any Country',
            'create' => 'Create Country',
            'update' => 'Update Country',
            'delete' => 'Delete Country',
        ],
        'event' => [
            'view' => 'View Events',
            'view.any' => 'View Any Event',
            'create' => 'Create Event',
            'update' => 'Update Event',
            'delete' => 'Delete Event',
        ],
        'package' => [
            'view' => 'View Packages',
            'view.any' => 'View Any Package',
            'create' => 'Create Package',
            'update' => 'Update Package',
            'delete' => 'Delete Package',
        ],
        'meeting' => [
            'view' => 'View Meetings',
            'view.any' => 'View Any Meeting',
            'create' => 'Create Meeting',
            'update' => 'Update Meeting',
            'delete' => 'Delete Meeting',
        ],
        'jobrun' => [
            'view' => 'View Job Runs',
            'view.any' => 'View Any Job Run',
        ],
    ];

    public function run(): void
    {
        $permissions = [];
        
        foreach (self::$permissionDefinitions as $resource => $actions) {
            foreach ($actions as $action => $name) {
                $slug = "{$resource}.{$action}";
                $permissions[] = [
                    'name' => $name,
                    'slug' => $slug,
                    'resource' => $resource,
                    'description' => $this->getDescription($resource, $action),
                ];
            }
        }

        $count = 0;
        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                [
                    'name' => $permission['name'],
                    'slug' => $permission['slug'],
                    'resource' => $permission['resource'] ?? null,
                    'description' => $permission['description'] ?? null,
                ]
            );
            $count++;
        }

        $this->command->info("Created/Updated {$count} permissions across " . count(self::$permissionDefinitions) . " resources.");
    }

    protected function getDescription(string $resource, string $action): string
    {
        $resourceName = ucfirst($resource);
        $actionMap = [
            'view' => "Can view {$resourceName} list",
            'view.any' => "Can view any {$resourceName} details",
            'create' => "Can create new {$resourceName}",
            'update' => "Can update {$resourceName}",
            'update.any' => "Can update any {$resourceName} (not just owned)",
            'delete' => "Can delete {$resourceName}",
            'delete.any' => "Can delete any {$resourceName} (not just owned)",
        ];

        return $actionMap[$action] ?? "Can {$action} {$resourceName}";
    }

    public static function getAllPermissionSlugs(): array
    {
        $slugs = [];
        foreach (self::$permissionDefinitions as $resource => $actions) {
            foreach (array_keys($actions) as $action) {
                $slugs[] = "{$resource}.{$action}";
            }
        }
        return $slugs;
    }

    public static function getPermissionsByResource(string $resource): array
    {
        if (!isset(self::$permissionDefinitions[$resource])) {
            return [];
        }

        $slugs = [];
        foreach (array_keys(self::$permissionDefinitions[$resource]) as $action) {
            $slugs[] = "{$resource}.{$action}";
        }
        return $slugs;
    }
}
