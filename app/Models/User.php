<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
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
