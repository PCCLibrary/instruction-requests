<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class RetryFailedMailJobs extends Command
{
    protected $signature = 'mail:retry-failed';
    protected $description = 'Retry all failed mail jobs';

    public function handle(): void
    {
        $failedJobs = DB::table('failed_jobs')
            ->where('queue', 'mail')
            ->get();

        foreach ($failedJobs as $job) {
            Artisan::call('queue:retry', ['id' => $job->uuid]);
        }

        $this->info("Retried {$failedJobs->count()} failed mail jobs");
    }
}
