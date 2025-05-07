<?php

namespace App\Console\Commands;

use App\Models\InstructionRequests;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FlushExpiredLocks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'locks:flushexpired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Release locks that have been inactive for more than 15 minutes';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $cutoffTime = now()->subMinutes(15);

        $staleLockedRequests = InstructionRequests::where('locked', true)
            ->where('locked_at', '<', $cutoffTime)
            ->get();

        $count = $staleLockedRequests->count();

        foreach ($staleLockedRequests as $request) {
            $this->info("Releasing stale lock on request {$request->id}");

            Log::info('Releasing stale lock', [
                'request_id' => $request->id,
                'locked_by' => $request->locked_by,
                'locked_at' => $request->locked_at,
                'inactive_for' => now()->diffInMinutes($request->locked_at) . ' minutes'
            ]);

            $request->markUnlocked();
        }

        $this->info("Released {$count} stale locks");
        return 0;
    }
}
