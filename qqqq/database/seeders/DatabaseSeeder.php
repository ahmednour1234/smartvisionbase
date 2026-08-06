<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = DB::table('roles')->where('slug', 'admin')->first();
        
        if ($adminRole) {
            DB::table('users')->updateOrInsert(
                ['email' => 'admin@smartvisioneg.com'],
                [
                    'name' => 'Admin',
                    'password' => Hash::make('password'),
                    'role_id' => $adminRole->id,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
        }

        DB::table('countries')->updateOrInsert(
            ['iso2' => 'EG'],
            ['name' => 'Egypt', 'created_at' => now(), 'updated_at' => now()],
        );

        DB::table('countries')->updateOrInsert(
            ['iso2' => 'AE'],
            ['name' => 'UAE', 'created_at' => now(), 'updated_at' => now()],
        );

        DB::table('packages')->updateOrInsert(
            ['name' => 'Basic'],
            ['price' => 0, 'created_at' => now(), 'updated_at' => now()],
        );

        $this->command->info('Seeding Permissions...');
        $this->call(PermissionSeeder::class);
        $this->command->info('Permissions seeded successfully!');

        $this->command->info('Seeding Roles...');
        $this->call(RoleSeeder::class);
        $this->command->info('Roles seeded successfully!');

        $this->command->info('Updating users with role_id...');
        $this->call(UpdateUsersRoleIdSeeder::class);
        $this->command->info('Users updated successfully!');

        if (env('SEED_PERFORMANCE') === '1') {
            $this->call(PerformanceSeeder::class);
        }
    }
}
