<?php

namespace App\Console\Commands;

use App\Models\TemporaryUpload;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanTemporaryUploads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uploads:clean {--days=1 : Number of days to keep files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired temporary uploads';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $expirationDate = now()->subDays($days);

        $this->info("Cleaning temporary uploads older than {$days} days...");

        // Find expired temporary uploads
        $expiredUploads = TemporaryUpload::where('expires_at', '<', now())
            ->orWhere('created_at', '<', $expirationDate)
            ->get();

        $count = $expiredUploads->count();
        $this->info("Found {$count} expired temporary uploads.");

        if ($count === 0) {
            return 0;
        }

        $deletedCount = 0;
        $errorCount = 0;

        foreach ($expiredUploads as $upload) {
            try {
                // Delete all associated media first (will delete the files from storage)
                $mediaIds = $upload->media->pluck('id')->toArray();
                $upload->clearMediaCollection('materials');

                // Then delete the temporary upload record
                $upload->delete();

                $deletedCount++;
                $this->info("Deleted temporary upload with token: {$upload->upload_token} and {$upload->media->count()} files");
                Log::info('Deleted expired temporary upload', [
                    'upload_token' => $upload->upload_token,
                    'media_ids' => $mediaIds,
                    'expires_at' => $upload->expires_at,
                    'created_at' => $upload->created_at
                ]);
            } catch (\Exception $e) {
                $errorCount++;
                $this->error("Error deleting temporary upload {$upload->id}: {$e->getMessage()}");
                Log::error('Error deleting temporary upload', [
                    'upload_id' => $upload->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        $this->info("Cleanup completed. Deleted {$deletedCount} temporary uploads with {$errorCount} errors.");
        
        return 0;
    }
}
