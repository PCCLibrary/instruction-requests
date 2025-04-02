<?php

namespace App\Services\MediaLibrary;

use Illuminate\Support\Facades\Log;
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
            'temporary' => $media->getCustomProperty('temporary', false)
        ]);

        // For temporary uploads (no model_id or temporary flag set)
        if (empty($media->model_id) || $media->model_id === 0 || $media->getCustomProperty('temporary', true)) {
            $path = 'uploads/temp/';

            Log::log($logLevel, 'Using temporary path', [
                'media_id' => $media->id,
                'path' => $path
            ]);

            return $path;
        }

        // Use year/month structure for new files (created after March 2025)
        if ($media->created_at && $media->created_at >= '2025-03-01') {
            $path = 'uploads/' . $media->created_at->format('Y/m') . '/';

            Log::log($logLevel, 'Using date-based path for new file', [
                'media_id' => $media->id,
                'path' => $path,
                'created_at' => $media->created_at
            ]);

            return $path;
        }

        // For existing files (created before March 2025), maintain the old structure
        $path = 'uploads/' . $media->model_id . '/';

        Log::log($logLevel, 'Using legacy path for existing file', [
            'media_id' => $media->id,
            'path' => $path,
            'created_at' => $media->created_at
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
        $basePath = $this->getPath($media);
        return $basePath . $media->collection_name . '/conversions/';
    }

    /**
     * Get the path for responsive images of the given media relative to the storage disk
     *
     * @param Media $media
     * @return string
     */
    public function getPathForResponsiveImages(Media $media): string
    {
        $basePath = $this->getPath($media);
        return $basePath . $media->collection_name . '/responsive-images/';
    }
}
