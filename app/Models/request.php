<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Class request
 * @package App\Models
 * @version January 27, 2024, 12:42 am UTC
 *
 * @property \App\Models\instructor $instructor
 * @property string $classname
 * @property string $description
 */
class request extends Model
{
    use SoftDeletes;


    public $table = 'requetsts';
    

    protected $dates = ['deleted_at'];



    public $fillable = [
        'classname',
        'description'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'classname' => 'string',
        'description' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     **/
    public function instructor()
    {
        return $this->hasOne(\App\Models\instructor::class, 'id', 'instructor');
    }
}
