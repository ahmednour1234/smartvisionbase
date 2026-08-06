<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PerformanceSeeder extends Seeder
{
    public function run(): void
    {
        $salesCount = (int) (env('PERF_SALES_COUNT', 100));
        $totalCompanies = (int) (env('PERF_COMPANY_COUNT', 100000));
        $batchSize = (int) (env('PERF_BATCH_SIZE', 1000));

        $salesRole = \App\Models\Role::where('slug', 'sales')->first();
        if ($salesRole) {
            User::factory()->count($salesCount)->create(['role_id' => $salesRole->id]);
        }

        $companiesInserted = 0;
        while ($companiesInserted < $totalCompanies) {
            $take = min($batchSize, $totalCompanies - $companiesInserted);
            $rows = [];

            for ($i = 0; $i < $take; $i++) {
                $seq = $companiesInserted + $i + 1;

                $name = ($seq % 1000 === 0)
                    ? ('شركة ' . $seq)
                    : ('Company ' . $seq);

                $normalized = (string) Str::of($name)
                    ->lower()
                    ->trim()
                    ->replaceMatches('/[^\p{L}\p{N}]/u', '');

                $rows[] = [
                    'company_name' => $name,
                    'normalized_company_name' => $normalized,
                    'status' => 'new',
                    'owner_id' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            DB::table('companies')->insert($rows);
            $companiesInserted += $take;
        }
    }
}
