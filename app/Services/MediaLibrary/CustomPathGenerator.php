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
            
            // Create directory if it doesn't exist
            if (!$this->ensureDirectoryExists($path)) {
                Log::error('Failed to ensure temporary directory exists', [
                    'media_id' => $media->id,
                    'path' => $path
                ]);
                // Continue anyway - we'll still return the path and let the caller handle failures
            }
            
            // Log the final path
            Log::log($logLevel, 'Generated temporary path', [
                'media_id' => $media->id,
                'path' => $path
            ]);
            
            return $path;
        }

        // Use year/month structure as default for new files
        $yearMonth = $media->created_at ? $media->created_at->format('Y/m') : date('Y/m');
        $path = 'uploads/' . $yearMonth . '/';
        
        // Check for legacy files (created before March 2025) - maintain old structure
        if ($media->created_at && $media->created_at < '2025-03-01' && !empty($media->model_id)) {
            $path = 'uploads/' . $media->model_id . '/';
            
            // Log using legacy path for existing file
            Log::log($logLevel, 'Using legacy ID-based path for existing file', [
                'media_id' => $media->id,
                'path' => $path,
                'created_at' => $media->created_at
            ]);
        }
        
        // Create directory if it doesn't exist
        if (!$this->ensureDirectoryExists($path)) {
            Log::error('Failed to ensure directory exists', [
                'media_id' => $media->id,
                'path' => $path
            ]);
            // Continue anyway - we'll still return the path and let the caller handle failures
        }
        
        // Log the final path
        Log::log($logLevel, 'Generated path', [
            'media_id' => $media->id,
            'path' => $path
        ]);
        
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
        // Set log level based on environment
        $logLevel = app()->environment('production') ? 'info' : 'debug';
        
        // For temporary uploads
        if (empty($media->model_id) || $media->model_id === 0 || $media->getCustomProperty('temporary', false)) {
            $path = 'uploads/temp/' . $media->collection_name . '/';
            
            // Create directory if it doesn't exist
            if (!$this->ensureDirectoryExists($path)) {
                Log::error('Failed to ensure temporary conversion directory exists', [
                    'media_id' => $media->id,
                    'path' => $path
                ]);
                // Continue anyway - we'll still return the path and let the caller handle failures
            }
            
            // Log the final path
            Log::log($logLevel, 'Generated temporary conversion path', [
                'media_id' => $media->id,
                'path' => $path
            ]);
            
            return $path;
        }

        // Use year/month structure as default for new files
        $yearMonth = $media->created_at ? $media->created_at->format('Y/m') : date('Y/m');
        $path = 'uploads/' . $yearMonth . '/' . $media->collection_name . '/';
        
        // Check for legacy files (created before March 2025) - maintain old structure
        if ($media->created_at && $media->created_at < '2025-03-01' && !empty($media->model_id)) {
            $path = 'uploads/' . $media->model_id . '/' . $media->collection_name . '/';
            
            // Log using legacy path for existing file
            Log::log($logLevel, 'Using legacy ID-based conversion path for existing file', [
                'media_id' => $media->id,
                'path' => $path,
                'created_at' => $media->created_at
            ]);
        }
        
        // Create directory if it doesn't exist
        if (!$this->ensureDirectoryExists($path)) {
            Log::error('Failed to ensure conversion directory exists', [
                'media_id' => $media->id,
                'path' => $path
            ]);
            // Continue anyway - we'll still return the path and let the caller handle failures
        }
        
        // Log the final path
        Log::log($logLevel, 'Generated conversion path', [
            'media_id' => $media->id,
            'path' => $path
        ]);
        
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
        $path = $this->getPathForConversions($media);
        
        // Set log level based on environment
        $logLevel = app()->environment('production') ? 'info' : 'debug';
        
        // Log the responsive images path
        Log::log($logLevel, 'Generated responsive images path', [
            'media_id' => $media->id,
            'path' => $path
        ]);
        
        return $path;
    }
    
    /**
     * Ensure directory exists and create it if it doesn't
     * This is a thorough implementation that handles nested paths and verifies directories
     *
     * @param string $path Directory path to ensure exists
     * @param string $disk Storage disk name (optional - defaults to config value)
     * @return bool True if directory exists or was created, false on failure
     */
    protected function ensureDirectoryExists(string $path, string $disk = null): bool
    {
        // Use provided disk or default to config value
        $disk = $disk ?? config('media-library.disk_name');
        
        try {
            // Remove trailing slash for consistency when checking
            $path = rtrim($path, '/');
            
            // If path is empty, assume it's root which always exists
            if (empty($path)) {
                return true;
            }
            
            // Check if directory already exists
            if (Storage::disk($disk)->exists($path)) {
                // Verify it's actually a directory, not a file
                try {
                    $files = Storage::disk($disk)->files($path);
                    // If we can list files in it, it's a directory
                    return true;
                } catch (\Exception $e) {
                    // If we can't list files, it might be a file with the same name
                    Log::warning("Path exists but may not be a directory", [
                        'path' => $path,
                        'disk' => $disk,
                        'error' => $e->getMessage()
                    ]);
                    // Try to delete and recreate if it's not a directory
                    try {
                        // Only do this for paths we expect to be directories
                        if (str_ends_with($path, '/') || str_contains($path, 'uploads/')) {
                            Storage::disk($disk)->delete($path);
                        } else {
                            return false; // Don't mess with paths we're not sure about
                        }
                    } catch (\Exception $ex) {
                        Log::error("Failed to delete non-directory path", [
                            'path' => $path,
                            'disk' => $disk,
                            'error' => $ex->getMessage()
                        ]);
                        return false;
                    }
                }
            }
            
            // Create the directory
            Log::info("Creating directory", [
                'path' => $path,
                'disk' => $disk
            ]);
            
            $created = Storage::disk($disk)->makeDirectory($path);
            
            // Verify creation
            if (!$created || !Storage::disk($disk)->exists($path)) {
                Log::error("Failed to create directory", [
                    'path' => $path,
                    'disk' => $disk,
                    'created_result' => $created,
                    'exists_after' => Storage::disk($disk)->exists($path)
                ]);
                return false;
            }
            
            Log::info("Directory created successfully", [
                'path' => $path,
                'disk' => $disk
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error("Exception during directory creation", [
                'path' => $path,
                'disk' => $disk,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
}