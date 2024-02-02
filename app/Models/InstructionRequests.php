<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Class InstructionRequests
 * @package App\Models
 * @version February 2, 2024, 5:47 pm UTC
 *
 * @property \App\Models\instructor $instructor
 * @property \App\Models\Campus $campus
 * @property string $instructor_name
 * @property string $display_name
 * @property string $instructor_email
 * @property string $instructor_phone
 * @property string $instruction_type
 * @property string $course_modality
 * @property string $librarian
 * @property string $class_location
 * @property string $course_department
 * @property string $course_number
 */
class InstructionRequests extends Model
{
    use SoftDeletes;


    public $table = 'instruction_requests';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
        'instructor_name',
        'display_name',
        'instructor_email',
        'instructor_phone',
        'instruction_type',
        'course_modality',
        'librarian',
        'class_location',
        'course_department',
        'course_number'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'instructor_name' => 'string',
        'display_name' => 'string',
        'instructor_email' => 'string',
        'instructor_phone' => 'string',
        'instruction_type' => 'string',
        'course_modality' => 'string',
        'librarian' => 'string',
        'class_location' => 'string',
        'course_department' => 'string',
        'course_number' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'instructor_name' => 'required',
        'instructor_email' => 'required'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     **/
    public function instructor()
    {
        return $this->hasOne(\App\Models\instructor::class, 'id', 'librarian');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     **/
    public function campus()
    {
        return $this->hasOne(\App\Models\Campus::class, 'id', 'class_location');
    }
}
