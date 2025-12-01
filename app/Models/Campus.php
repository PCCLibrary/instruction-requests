<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

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
        'sort_order',
    ];

    // Cast attributes to specific data types
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'code' => 'string',
        'gcal' => 'string',
        'librarian_ids' => 'array', // Automatically cast JSON to array
        'sort_order' => 'integer',
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
     * Get the instruction requests for this campus.
     *
     * @return HasMany
     */
    public function instructionRequests(): HasMany
    {
        return $this->hasMany(InstructionRequests::class, 'campus_id');
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
     * Returns the calendar ID stored in the gcal field, validated for the correct format.
     *
     * @return string|null The calendar ID or null if not set or invalid
     */
    public function getCalendarId(): ?string
    {
        // Check if the gcal field is empty
        if (empty($this->gcal)) {
            Log::warning('Campus: gcal field is empty', [
                'campus_id' => $this->id,
                'campus_name' => $this->name,
            ]);
            return null;
        }

        // Trim the gcal value to remove any leading/trailing spaces
        $calendarId = trim($this->gcal);

        // Validate the format of the calendar ID
        $isValidFormat = preg_match('/^[a-z0-9._]+@group\.calendar\.google\.com$/', $calendarId);

        if (!$isValidFormat) {
            Log::error('Campus: Invalid Google Calendar ID format', [
                'campus_id' => $this->id,
                'campus_name' => $this->name,
                'gcal_value' => $this->gcal,
            ]);
            return null;
        }

        return $calendarId;
    }

    /**
     * Scope a query to order campuses by sort_order then name.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Scope to exclude "No Campus Preference" for librarian-related contexts.
     * This is used when filtering librarians or assigning campuses to users,
     * as librarians must have a specific campus assignment.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForLibrarians($query)
    {
        return $query->where('name', '!=', 'No Campus Preference')
                     ->orderBy('sort_order')
                     ->orderBy('name');
    }
}
