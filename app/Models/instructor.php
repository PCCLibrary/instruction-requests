<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Class instructor
 * @package App\Models
 * @version January 26, 2024, 11:59 pm UTC
 *
 * @property string $name
 * @property string $display_name
 * @property string $email
 * @property string $phone
 */
class instructor extends Model
{
    use SoftDeletes;


    public $table = 'instructors';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
        'name',
        'display_name',
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
