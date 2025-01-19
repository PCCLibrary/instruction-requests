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
}
