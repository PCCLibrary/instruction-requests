<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
//use Laravel\Sanctum\HasApiTokens;
use LakM\Comments\Concerns\Commenter;
use LakM\Comments\Contracts\CommenterContract;

class User extends Authenticatable implements CommenterContract
{
    use  HasFactory, Notifiable, Commenter;

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
        'password',
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
    ];

    /**
     * Order librarians with priority accounts first, then alphabetically
     */
    public static function orderedLibrariansScope(): Builder
    {
        return static::query()
            ->where('is_admin', false)
            ->orderByRaw('FIELD(id, ' . implode(',', static::$priorityLibrarianIds) . ') DESC')
            ->orderBy('display_name');
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
        // Define the many-to-many relationship
        return $this->belongsToMany(Campus::class, 'campus_user', 'librarian_id', 'campus_id')
            ->withTimestamps(); // Optional: if you want to automatically update created_at and updated_at timestamps in pivot table
    }


}
