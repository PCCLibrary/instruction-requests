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

    /**
     * @var string
     */
    public $table = 'instructors';

    /**
     * @var string[]
     */
    protected $dates = ['deleted_at'];

    /**
     * @var string[]
     */
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
    public static array $rules = [
        'name' => 'required|string|max:255',
        'display_name' => 'required|string|max:255',
        'pronouns' => 'nullable|string|max:255',
        'email' => 'required|email|unique:instructors,email',
        'phone' => 'nullable|string|max:20'
    ];


    /**
     * @return array|string[]
     */
    public function rules(): array
    {
        $rules = Instructor::$rules;
        $rules['email'] = 'required|email|unique:instructors,email,' . $this->route('instructor');
        return $rules;
    }


}
