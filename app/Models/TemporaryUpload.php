<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class TemporaryUpload extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'temporary_uploads';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'upload_token',
        'expires_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function ($temporaryUpload) {
            // Set expiration date to 2 hours from now if not provided
            if (!$temporaryUpload->expires_at) {
                $temporaryUpload->expires_at = now()->addHours(2);
            }
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'upload_token';
    }

    /**
     * Determine if the upload has expired.
     */
    public function hasExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Register media collections.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('materials')
            ->acceptsMimeTypes([
                // PDF
                'application/pdf',

                // Word Documents
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',

                // PowerPoint
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',

                // Text Files
                'text/plain',
                'text/rtf',
                'application/rtf',

                // Excel Files
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',

                // OpenDocument Formats
                'application/vnd.oasis.opendocument.text',
                'application/vnd.oasis.opendocument.spreadsheet',
                'application/vnd.oasis.opendocument.presentation',

                // Rich Text Format variants
                'application/x-rtf',

                // HTML (for web-based materials)
                'text/html',

                // ePub (for digital books)
                'application/epub+zip'
            ])
            ->useDisk(config('media-library.disk_name'));
    }
}
