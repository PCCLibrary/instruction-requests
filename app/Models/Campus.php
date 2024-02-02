<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Class Campus
 * @package App\Models
 * @version February 1, 2024, 11:27 pm UTC
 *
 * @property string $name
 * @property string $code
 */
class Campus extends Model
{
    use SoftDeletes;


    public $table = 'campuses';

    protected $dates = ['deleted_at'];


    public $fillable = [
        'name',
        'code'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'code' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];


}
