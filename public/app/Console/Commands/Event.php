<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Event extends Command
{
    /**
     * اسم الكوماند:
     * php artisan events:update
     */
    protected $signature   = 'events:update {--force : Force the operation even in production}';

    protected $description = 'Dangerous: drop ALL tables from the current database connection';

    public function handle()
    {
        $env = app()->environment();

        // أمان شوية في الـ production

        // تأكيد من المستخدم
  

        $connection = DB::connection();
        $driver     = $connection->getDriverName();

        $this->info("Using connection: {$connection->getName()} (driver: {$driver})");

        // نجيب كل الجداول بدون Doctrine
        $tables = $this->getAllTableNames($connection, $driver);

        if (empty($tables)) {
            $this->info('No tables found.');
            return Command::SUCCESS;
        }

        Schema::disableForeignKeyConstraints();

        foreach ($tables as $table) {
            $this->line("Dropping table: {$table}");
            Schema::drop($table);
        }

        Schema::enableForeignKeyConstraints();

        $this->info('✅ All tables dropped successfully.');
        return Command::SUCCESS;
    }

    /**
     * جلب أسماء كل الجداول حسب نوع قاعدة البيانات بدون doctrine/dbal
     */
    protected function getAllTableNames($connection, string $driver): array
    {
        $tables = [];

        switch ($driver) {
            case 'mysql':
            case 'mariadb':
                $dbName = $connection->getDatabaseName();
                $result = $connection->select('SHOW TABLES');
                $key    = 'Tables_in_'.$dbName;

                foreach ($result as $row) {
                    if (isset($row->$key)) {
                        $tables[] = $row->$key;
                    }
                }
                break;

            case 'pgsql':
                $result = $connection->select("SELECT tablename FROM pg_tables WHERE schemaname = 'public'");
                foreach ($result as $row) {
                    $tables[] = $row->tablename;
                }
                break;

            case 'sqlite':
                $result = $connection->select("SELECT name FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%'");
                foreach ($result as $row) {
                    $tables[] = $row->name;
                }
                break;

            case 'sqlsrv':
                $result = $connection->select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE'");
                foreach ($result as $row) {
                    $tables[] = $row->TABLE_NAME;
                }
                break;

            default:
                throw new \RuntimeException("Unsupported driver: {$driver}");
        }

        return $tables;
    }
}
