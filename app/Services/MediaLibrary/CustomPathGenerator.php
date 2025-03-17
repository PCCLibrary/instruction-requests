<?php

namespace App\Services\MediaLibrary;

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
        // For temporary uploads (no model_id or id=0)
        if (empty($media->model_id) || $media->model_id === 0 || $media->getCustomProperty('temporary', false)) {
            return 'uploads/temp/';
        }

        // Use year/month structure for new files 
        // (created after this update in March 2025)
        if ($media->created_at >= '2025-03-01') {
            return 'uploads/' . $media->created_at->format('Y/m') . '/';
        }

        // For existing files, maintain the old structure
        return 'uploads/' . $media->model->id . '/';
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
            return 'uploads/temp/' . $media->collection_name . '/';
        }

        // Use year/month structure for new files
        if ($media->created_at >= '2025-03-01') {
            return 'uploads/' . $media->created_at->format('Y/m') . '/' . $media->collection_name . '/';
        }

        // For existing files, maintain the old structure
        return 'uploads/' . $media->model->id . '/' . $media->collection_name . '/';
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