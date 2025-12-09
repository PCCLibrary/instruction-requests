<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
//use Laravel\Sanctum\HasApiTokens;
use LakM\Comments\Concerns\Commenter;
use LakM\Comments\Contracts\CommenterContract;

class User extends Authenticatable implements CommenterContract
{
    use  HasFactory, Notifiable, Commenter, SoftDeletes;

    /**
     * The priority of librarian ids.
     *
     * @var array<int, int>
     */
    public static array $priorityLibrarianIds = [
        6,  // Sylvania Librarian
        5,  // Southeast Librarian
        4,  // Rock Creek Librarian
        3,  // Cascade Librarian
        2   // No librarian preference
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'display_name',
        'campus_id',
        'email',
        'is_scheduler',
        'available_for_assignment'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'campus_id' => 'integer',
        'is_scheduler' => 'boolean',
        'is_admin' => 'boolean',
        'available_for_assignment' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    /**
     * Order librarians with priority accounts first, then alphabetically
     * Note: With SoftDeletes trait, this automatically excludes soft-deleted users
     */
    public static function orderedLibrariansScope(): Builder
    {
        return static::query()
            ->where(function ($query) {
                $query->where('is_admin', false)
                      ->orWhere(function ($subQuery) {
                          $subQuery->where('is_admin', true)
                                   ->where('available_for_assignment', true);
                      });
            })
            ->orderByRaw('FIELD(id, ' . implode(',', static::$priorityLibrarianIds) . ') DESC')
            ->orderBy('display_name');
    }

    /**
     * Scope for all librarians including soft-deleted ones
     * Useful for administrative views or historical data
     */
    public static function allLibrariansScope(): Builder
    {
        return static::withTrashed()
            ->where(function ($query) {
                $query->where('is_admin', false)
                      ->orWhere(function ($subQuery) {
                          $subQuery->where('is_admin', true)
                                   ->where('available_for_assignment', true);
                      });
            })
            ->orderByRaw('FIELD(id, ' . implode(',', static::$priorityLibrarianIds) . ') DESC')
            ->orderBy('display_name');
    }

    /**
     * Scope for only soft-deleted librarians
     * Useful for restoration/cleanup operations
     */
    public static function deletedLibrariansScope(): Builder
    {
        return static::onlyTrashed()
            ->where(function ($query) {
                $query->where('is_admin', false)
                      ->orWhere(function ($subQuery) {
                          $subQuery->where('is_admin', true)
                                   ->where('available_for_assignment', true);
                      });
            })
            ->orderByRaw('FIELD(id, ' . implode(',', static::$priorityLibrarianIds) . ') DESC')
            ->orderBy('display_name');
    }

    /**
     * General scope for active users (non-librarian contexts)
     * Note: Automatically excludes soft-deleted when SoftDeletes trait is used
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query;
    }

    /**
     * Scope for all users including soft-deleted
     */
    public function scopeWithDeleted(Builder $query): Builder
    {
        return $query->withTrashed();
    }

    /**
     * @return BelongsTo
     */
    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    /**
     * The campuses that the librarian is associated with.
     *
     * @return BelongsToMany
     */
    public function campuses(): BelongsToMany
    {
        return $this->belongsToMany(Campus::class, 'campus_user', 'user_id', 'campus_id')
            ->withTimestamps()
            ->orderBy('sort_order')
            ->orderBy('name');
    }


}
