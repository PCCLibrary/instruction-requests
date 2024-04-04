<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;


/**
 * Class Instructor
 * @package App\Models
 * @version January 26, 2024, 11:59 pm UTC
 *
 * @property string $name
 * @property string $display_name
 * @property string $pronouns
 * @property string $email
 * @property string $phone
 */
class Instructor extends Model
{
    use SoftDeletes, Notifiable;

    public $table = 'instructors';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'name',
        'display_name',
        'pronouns',
        'email',
        'phone'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'display_name' => 'string',
        'pronouns' => 'string',
        'email' => 'string',
        'phone' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];


}
