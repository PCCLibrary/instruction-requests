<?php

namespace App\Services\MediaLibrary;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class CustomPathGenerator implements PathGenerator
{
    /**
     * Get the path for the given media relative to the storage disk
     *
     * @param Media $media
     * @return string
     */
    public function getPath(Media $media): string
    {
        // Set log level based on environment
        $logLevel = app()->environment('production') ? 'info' : 'debug';
        
        // Log at appropriate level
        Log::log($logLevel, 'Generating path for media', [
            'media_id' => $media->id,
            'model_id' => $media->model_id,
            'model_type' => $media->model_type,
            'collection' => $media->collection_name,
            'created_at' => $media->created_at,
            'temporary' => $media->getCustomProperty('temporary', false),
            'environment' => app()->environment()
        ]);

        // For temporary uploads (no model_id or id=0)
        if (empty($media->model_id) || $media->model_id === 0 || $media->getCustomProperty('temporary', false)) {
            $path = 'uploads/temp/';
            $this->ensureDirectoryExists($path);
            return $path;
        }

        // Use year/month structure for new files (created after March 2025)
        if ($media->created_at >= '2025-03-01') {
            $path = 'uploads/' . $media->created_at->format('Y/m') . '/';
            $this->ensureDirectoryExists($path);
            return $path;
        }

        // For existing files, maintain the old structure
        $path = 'uploads/' . $media->model->id . '/';
        $this->ensureDirectoryExists($path);
        return $path;
    }

    /**
     * Get the path for conversions of the given media relative to the storage disk
     *
     * @param Media $media
     * @return string
     */
    public function getPathForConversions(Media $media): string
    {
        // For temporary uploads
        if (empty($media->model_id) || $media->model_id === 0 || $media->getCustomProperty('temporary', false)) {
            $path = 'uploads/temp/' . $media->collection_name . '/';
            $this->ensureDirectoryExists($path);
            return $path;
        }

        // Use year/month structure for new files
        if ($media->created_at >= '2025-03-01') {
            $path = 'uploads/' . $media->created_at->format('Y/m') . '/' . $media->collection_name . '/';
            $this->ensureDirectoryExists($path);
            return $path;
        }

        // For existing files, maintain the old structure
        $path = 'uploads/' . $media->model->id . '/' . $media->collection_name . '/';
        $this->ensureDirectoryExists($path);
        return $path;
    }

    /**
     * Get the path for responsive images of the given media relative to the storage disk
     *
     * @param Media $media
     * @return string
     */
    public function getPathForResponsiveImages(Media $media): string
    {
        // Return the same path as conversions
        return $this->getPathForConversions($media);
    }
    
    /**
     * Ensure a directory exists on the storage disk
     *
     * @param string $path
     * @return void
     */
    protected function ensureDirectoryExists(string $path): void
    {
        $disk = config('media-library.disk_name');
        
        try {
            if (!Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->makeDirectory($path);
                
                // Log directory creation in non-production environments
                if (!app()->environment('production')) {
                    Log::debug('Created directory for media files', [
                        'path' => $path,
                        'disk' => $disk,
                        'environment' => app()->environment()
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to create directory for media files', [
                'path' => $path,
                'disk' => $disk,
                'error' => $e->getMessage(),
                'environment' => app()->environment()
            ]);
        }
    }
}
