<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('password')->constrained('roles')->nullOnDelete();
        });

        $roleMapping = [
            'admin' => 'admin',
            'manager' => 'manager',
            'sales' => 'sales',
        ];

        foreach ($roleMapping as $oldRole => $slug) {
            $role = DB::table('roles')->where('slug', $slug)->first();
            if ($role) {
                DB::table('users')
                    ->where('role', $oldRole)
                    ->update(['role_id' => $role->id]);
            }
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'manager', 'sales'])->default('sales')->after('password');
        });

        $users = DB::table('users')->whereNotNull('role_id')->get();
        foreach ($users as $user) {
            $role = DB::table('roles')->where('id', $user->role_id)->first();
            if ($role) {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['role' => $role->slug]);
            }
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });
    }
};
