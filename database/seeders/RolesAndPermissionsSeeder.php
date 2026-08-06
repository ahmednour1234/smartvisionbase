<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $perms = [
            'dashboard.view',
            'lookups.view',
            'lookups.manage',

            'leads.view',
            'leads.create',
            'leads.update',
            'leads.delete',

            'users.manage',
            'audit.view',
            'meetings.manage',
        ];

        foreach ($perms as $p) {
            Permission::findOrCreate($p, 'web');
        }

        $admin = Role::findOrCreate('admin', 'web');
        $staff = Role::findOrCreate('staff', 'web');

        $admin->syncPermissions(Permission::where('guard_name', 'web')->get());

        $staff->syncPermissions([
            'dashboard.view',
            'lookups.view',

            'leads.view',
            'leads.create',
            'leads.update',

            'meetings.manage',
        ]);
    }
}
