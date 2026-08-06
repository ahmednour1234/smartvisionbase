<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $indexName = 'leads_company_contact_ft';

    public function up(): void
    {
        // MySQL only.
        try {
            if (DB::getDriverName() !== 'mysql') {
                return;
            }
        } catch (Throwable $e) {
            return;
        }

        if (!Schema::hasTable('leads')) {
            return;
        }

        // Idempotent: skip if index already exists.
        try {
            $exists = DB::select('SHOW INDEX FROM `leads` WHERE Key_name = ?', [$this->indexName]);
            if (!empty($exists)) {
                return;
            }
        } catch (Throwable $e) {
            // Continue best-effort.
        }

        // Fulltext index for enterprise search.
        try {
            DB::statement("ALTER TABLE `leads` ADD FULLTEXT `{$this->indexName}` (`company_name`, `contact_person`)");
        } catch (Throwable $e) {
            // If the DB version/engine doesn't support it, fail gracefully.
            // Search will fall back to prefix matching.
        }
    }

    public function down(): void
    {
        try {
            if (DB::getDriverName() !== 'mysql') {
                return;
            }
        } catch (Throwable $e) {
            return;
        }

        if (!Schema::hasTable('leads')) {
            return;
        }

        try {
            $exists = DB::select('SHOW INDEX FROM `leads` WHERE Key_name = ?', [$this->indexName]);
            if (empty($exists)) {
                return;
            }
            DB::statement("ALTER TABLE `leads` DROP INDEX `{$this->indexName}`");
        } catch (Throwable $e) {
            // ignore
        }
    }
};
