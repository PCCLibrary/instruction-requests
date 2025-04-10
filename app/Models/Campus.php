<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campus extends Model
{
    use SoftDeletes;

    // Define table name
    public $table = 'campuses';

    // Enable soft delete timestamps
    protected $dates = ['deleted_at'];

    // Mass assignable fields
    protected $fillable = [
        'name',
        'code',
        'gcal',
        'librarian_ids', // Ensure this aligns with the database schema
    ];

    // Cast attributes to specific data types
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'code' => 'string',
        'gcal' => 'string',
        'librarian_ids' => 'array', // Automatically cast JSON to array
    ];

    /**
     * Get the librarians associated with the campus.
     *
     * Assumes there's a users relationship where some are librarians.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function librarians()
    {
        return $this->belongsToMany(
            User::class,    // Target model
            'campus_user',  // Pivot table name (assumed)
            'campus_id',    // Foreign key on pivot table
            'user_id'       // Related key on pivot table
        );
    }

    /**
     * Scope a query to search campuses by name or code.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $filter
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, ?string $filter)
    {
        return $query->when($filter, function ($q, $filter) {
            $q->where('name', 'like', "%{$filter}%")
                ->orWhere('code', 'like', "%{$filter}%");
        });
    }

    /**
     * Get the Google Calendar ID for this campus.
     * 
     * Returns the calendar ID stored in the gcal field if it follows the correct format.
     * This method does not modify or extract from URLs - it expects the gcal field
     * to already contain a valid calendar ID.
     *
     * @param bool $validateFormat Whether to validate the format of the calendar ID
     * @return string|null The calendar ID or null if not set or invalid
     */
    public function getCalendarId(bool $validateFormat = true): ?string
    {
        if (empty($this->gcal)) {
            return null;
        }

        // If no validation requested, return as is
        if (!$validateFormat) {
            return $this->gcal;
        }

        // Check if the value matches the format of a Google Calendar ID
        // Examples:
        // - email format: example@group.calendar.google.com
        // - alphanumeric format: c_91e4e2a58503a3f41186894f62e70d8f904be3e72af56f6059fb4f0220b7dbc4@group.calendar.google.com
        if (preg_match('/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/', $this->gcal) || 
            preg_match('/^c_[a-zA-Z0-9]+(@group\.calendar\.google\.com)?$/', $this->gcal)) {
            return $this->gcal;
        }
        
        // Log warning if format is invalid
        \Illuminate\Support\Facades\Log::warning('Invalid Google Calendar ID format in campus', [
            'campus_id' => $this->id,
            'campus_name' => $this->name,
            'gcal_value' => $this->gcal
        ]);
        
        return $this->gcal; // Return anyway, even if format validation fails
    }
}
