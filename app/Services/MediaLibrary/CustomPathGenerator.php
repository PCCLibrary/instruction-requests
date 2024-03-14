<?php

namespace App\Services\MediaLibrary;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class CustomPathGenerator implements PathGenerator
{

    public function getPath(Media $media): string
    {
        // Use instruction_request_id to organize files at the root
        return 'uploads/' . $media->model->id . '/';
    }

    public function getPathForConversions(Media $media): string
    {
        // Directories for specific file types, using collection name
        return 'uploads/' . $media->model->id . '/' . $media->collection_name . '/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        // If you don't plan to use responsive images, just return a default or same as conversions
        return $this->getPathForConversions($media);
    }
}
