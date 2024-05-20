<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Class Classes
 * @package App\Models
 * @version February 26, 2024, 7:59 pm UTC
 *
 * @property string $department_code
 * @property string $course_number
 * @property string $course_name
 * @property string $course_crn

 */
class Classes extends Model
{
    use SoftDeletes;


    public $table = 'classes';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'department_code',
        'course_number',
        'course_name',
        'course_crn'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'department_code' => 'string',
        'course_number' => 'string',
        'course_name' => 'string',
        'course_crn' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];


}
