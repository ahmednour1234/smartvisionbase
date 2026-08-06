<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CountriesSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/seed_countries.sql');
        if (!File::exists($path)) {
            return;
        }

        $sql = File::get($path);
        // Remove USE/SET NAMES if present
        $sql = preg_replace('/^\s*USE\s+.*?;\s*$/mi', '', $sql);
        $sql = preg_replace('/^\s*SET\s+NAMES\s+.*?;\s*$/mi', '', $sql);

        DB::unprepared($sql);
    }
}
