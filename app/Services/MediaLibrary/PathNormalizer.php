<?php

namespace App\Services\MediaLibrary;

use Illuminate\Support\Facades\Log;

/**
 * Helper class to normalize paths for the media library
 * Ensures paths are consistent and prevent duplication
 */
class PathNormalizer
{
    /**
     * Normalize a path to prevent duplications and ensure consistent format
     *
     * @param string $path
     * @return string
     */
    public static function normalize(string $path): string
    {
        // Remove leading and trailing slashes
        $path = trim($path, '/');
        
        // Remove potential duplications of storage path
        $storagePath = 'storage/app/public/';
        if (strpos($path, $storagePath) === 0) {
            $path = substr($path, strlen($storagePath));
        }
        
        // Remove potential full path duplications 
        $fullStoragePath = storage_path('app/public/');
        if (strpos($path, $fullStoragePath) === 0) {
            $path = substr($path, strlen($fullStoragePath));
        }
        
        // Prevent double path issues
        $publicPath = public_path('storage/');
        if (strpos($path, $publicPath) === 0) {
            $path = substr($path, strlen($publicPath));
        }
        
        // Handle potential double path duplication
        // Match patterns like /var/www/...storage/app/public/var/www/...storage/app/public/
        if (preg_match('/(.*?storage\/app\/public\/)(.*)/', $path, $matches)) {
            // Keep only what's after the last storage/app/public/
            $path = $matches[2];
        }
        
        // Log the normalized path for debugging
        if (!app()->environment('production')) {
            Log::debug('Path normalized', [
                'original' => $path, 
                'normalized' => $path
            ]);
        }
        
        return $path;
    }
    
    /**
     * Get path for direct file storage for a given media
     * Ensures consistent path structure and prevents duplication
     *
     * @param string $baseDir Base directory relative to storage disk
     * @param string $filename Filename
     * @return string Full path for storage
     */
    public static function getFilePath(string $baseDir, string $filename): string
    {
        // Normalize base directory
        $baseDir = self::normalize($baseDir);
        
        // Clean filename
        $filename = basename($filename);
        
        // Combine path - ensure no double slashes
        $path = rtrim($baseDir, '/') . '/' . $filename;
        
        return $path;
    }
    
    /**
     * Check if a file exists in multiple possible locations
     * Handles different path conventions
     *
     * @param string $filename Filename to look for
     * @param array $directories Directories to check
     * @param string $disk Storage disk name
     * @return array|null Path details if found, null if not found
     */
    public static function findFile(string $filename, array $directories, string $disk = 'public'): ?array
    {
        $storage = \Illuminate\Support\Facades\Storage::disk($disk);
        
        // Check each directory for the file
        foreach ($directories as $type => $dir) {
            $basePath = self::normalize($dir);
            $path = rtrim($basePath, '/') . '/' . $filename;
            
            if ($storage->exists($path)) {
                return [
                    'path' => $path,
                    'directory' => $basePath,
                    'type' => $type,
                    'exists' => true,
                    'size' => $storage->size($path)
                ];
            }
        }
        
        // Check alternative simple locations
        $alternativePaths = [
            'alt_temp' => 'uploads/temp/' . $filename,
            'alt_root' => $filename,
        ];
        
        foreach ($alternativePaths as $type => $path) {
            if ($storage->exists($path)) {
                return [
                    'path' => $path,
                    'directory' => dirname($path),
                    'type' => $type,
                    'exists' => true,
                    'size' => $storage->size($path)
                ];
            }
        }
        
        return null;
    }
}