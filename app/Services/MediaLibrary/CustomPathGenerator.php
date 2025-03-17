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
        // Log path generation for debugging
        Log::info('Generating path for media', [
            'media_id' => $media->id,
            'model_id' => $media->model_id,
            'model_type' => $media->model_type,
            'collection' => $media->collection_name,
            'created_at' => $media->created_at,
            'temporary' => $media->getCustomProperty('temporary', false)
        ]);
        
        // For temporary uploads (no model_id or id=0)
        if (empty($media->model_id) || $media->model_id === 0 || $media->getCustomProperty('temporary', false)) {
            $path = 'uploads/temp/';
            Log::info('Using temporary path', ['path' => $path]);
            return $path;
        }

        // Use year/month structure for new files
        // (created after this update in March 2025)
        if ($media->created_at >= '2025-03-01') {
            $path = 'uploads/' . $media->created_at->format('Y/m') . '/';
            Log::info('Using date-based path', ['path' => $path]);
            return $path;
        }

        // For existing files, maintain the old structure
        $path = 'uploads/' . $media->model->id . '/';
        Log::info('Using request ID-based path', ['path' => $path]);
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
        // Log conversion path generation
        Log::info('Generating conversion path for media', [
            'media_id' => $media->id,
            'model_id' => $media->model_id,
            'collection' => $media->collection_name
        ]);
        
        // For temporary uploads
        if (empty($media->model_id) || $media->model_id === 0 || $media->getCustomProperty('temporary', false)) {
            $path = 'uploads/temp/' . $media->collection_name . '/';
            Log::info('Using temporary conversion path', ['path' => $path]);
            return $path;
        }

        // Use year/month structure for new files
        if ($media->created_at >= '2025-03-01') {
            $path = 'uploads/' . $media->created_at->format('Y/m') . '/' . $media->collection_name . '/';
            Log::info('Using date-based conversion path', ['path' => $path]);
            return $path;
        }

        // For existing files, maintain the old structure
        $path = 'uploads/' . $media->model->id . '/' . $media->collection_name . '/';
        Log::info('Using request ID-based conversion path', ['path' => $path]);
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
}
