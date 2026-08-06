<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateUsersRoleIdSeeder extends Seeder
{
    public function run(): void
    {
        $roles = DB::table('roles')->get()->keyBy('id');
        $validRoleIds = $roles->pluck('id')->toArray();
        
        $defaultRole = DB::table('roles')->where('slug', 'sales')->first();
        
        if (!$defaultRole) {
            $this->command->warn('Sales role not found. Please run RoleSeeder first.');
            return;
        }

        $usersWithoutRole = DB::table('users')->whereNull('role_id')->get();
        $count1 = 0;
        foreach ($usersWithoutRole as $user) {
            DB::table('users')
                ->where('id', $user->id)
                ->update(['role_id' => $defaultRole->id]);
            $count1++;
            $this->command->info("Updated user {$user->email} (no role_id) with role_id {$defaultRole->id}");
        }

        $usersWithInvalidRole = DB::table('users')
            ->whereNotNull('role_id')
            ->whereNotIn('role_id', $validRoleIds)
            ->get();
        $count2 = 0;
        foreach ($usersWithInvalidRole as $user) {
            DB::table('users')
                ->where('id', $user->id)
                ->update(['role_id' => $defaultRole->id]);
            $count2++;
            $this->command->info("Updated user {$user->email} (invalid role_id {$user->role_id}) with role_id {$defaultRole->id}");
        }

        $this->command->info("Updated {$count1} users without role_id and {$count2} users with invalid role_id.");
        $this->command->info('All users now have valid role_id assigned.');
    }
}
