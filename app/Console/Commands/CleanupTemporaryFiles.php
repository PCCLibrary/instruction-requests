<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CleanupTemporaryFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:cleanup-temp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up temporary files that are older than 24 hours';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Cleaning up temporary files...');
        
        try {
            // Find temporary files older than 24 hours
            $cutoffDate = now()->subHours(24);
            
            $tempFiles = Media::where('model_id', 0)
                ->where(function ($query) {
                    $query->where('custom_properties->temporary', true)
                        ->orWhereJsonContains('custom_properties', ['temporary' => true]);
                })
                ->where('created_at', '<', $cutoffDate)
                ->get();
            
            $count = $tempFiles->count();
            $this->info("Found {$count} temporary files to clean up.");
            
            // Delete each file
            foreach ($tempFiles as $file) {
                $this->line("Deleting {$file->file_name}...");
                $file->delete();
            }
            
            $this->info('Temporary files cleanup completed.');
            Log::info("Cleaned up {$count} temporary files.");
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error cleaning up temporary files: ' . $e->getMessage());
            Log::error('Error cleaning up temporary files: ' . $e->getMessage());
            
            return Command::FAILURE;
        }
    }
}
