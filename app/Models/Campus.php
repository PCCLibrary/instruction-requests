<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        'code',
        'librarian_ids' // Ensure this is fillable if you're planning to mass assign.

    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'code' => 'string',
        'librarian_ids' => 'array' // Cast the librarian_ids field to an array.

    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];


}
