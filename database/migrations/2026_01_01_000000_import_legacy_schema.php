<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

return new class extends Migration {
    public function up(): void
    {
        $base = database_path('sql');
        $schemaFile = $base . DIRECTORY_SEPARATOR . 'schema_core.sql';
        $idxFile = $base . DIRECTORY_SEPARATOR . 'add_indexes_v6_3_2.sql';

        if (File::exists($schemaFile)) {
            DB::unprepared(File::get($schemaFile));
        }
        if (File::exists($idxFile)) {
            DB::unprepared(File::get($idxFile));
        }
    }

    public function down(): void
    {
        // Drop tables in reverse-ish order
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        foreach (['audit_log','lead_activities','meetings','lead_countries','leads','packages','lost_categories','events','countries','users','job_runs'] as $t) {
            DB::statement("DROP TABLE IF EXISTS $t");
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
