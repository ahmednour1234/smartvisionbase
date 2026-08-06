<?php

namespace App\Console\Commands;

use App\Models\JobRun;
use Illuminate\Console\Command;

class DailyFollowupCommand extends Command
{
    protected $signature = 'crm:daily-followup';

    protected $description = 'Log the daily follow-up cron run (placeholder).';

    public function handle(): int
    {
        JobRun::create([
            'job_name' => 'DailyFollowup',
            'status' => 'success',
            'started_at' => now(),
            'finished_at' => now(),
        ]);

        $this->info('Daily follow-up job logged.');

        return self::SUCCESS;
    }
}
