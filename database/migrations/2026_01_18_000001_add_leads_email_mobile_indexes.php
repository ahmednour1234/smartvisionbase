<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    private function indexExists(string $table, string $indexName): bool
    {
        try {
            $dbName = DB::getDatabaseName();
            $row = DB::selectOne(
                'SELECT 1 FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ? LIMIT 1',
                [$dbName, $table, $indexName]
            );
            return (bool) $row;
        } catch (\Throwable $e) {
            // If information_schema is blocked, fall back to attempting the statement.
            return false;
        }
    }

    public function up(): void
    {
        // Add indexes safely for common search fields.
        // NOTE: MySQL has no ADD INDEX IF NOT EXISTS until very recent versions; we check information_schema first.

        if (!$this->indexExists('leads', 'idx_leads_contact_email_v632')) {
            try {
                DB::statement('CREATE INDEX idx_leads_contact_email_v632 ON leads (contact_email)');
            } catch (\Throwable $e) {
                // Ignore "already exists" errors in case of race/deploy re-run.
            }
        }

        if (!$this->indexExists('leads', 'idx_leads_contact_mobile_v632')) {
            try {
                DB::statement('CREATE INDEX idx_leads_contact_mobile_v632 ON leads (contact_mobile)');
            } catch (\Throwable $e) {
                // Ignore "already exists" errors in case of race/deploy re-run.
            }
        }
    }

    public function down(): void
    {
        if ($this->indexExists('leads', 'idx_leads_contact_email_v632')) {
            try {
                DB::statement('DROP INDEX idx_leads_contact_email_v632 ON leads');
            } catch (\Throwable $e) {
                // ignore
            }
        }

        if ($this->indexExists('leads', 'idx_leads_contact_mobile_v632')) {
            try {
                DB::statement('DROP INDEX idx_leads_contact_mobile_v632 ON leads');
            } catch (\Throwable $e) {
                // ignore
            }
        }
    }
};
