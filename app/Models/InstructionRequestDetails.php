<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class InstructionRequestDetails
 * @package App\Models
 * @version February 26, 2024, 10:37 pm UTC
 *
 * @property unsignedInteger $instruction_request_id
 * @property unsignedBigInteger $librarian_id
 * @property json $tasks_completed
 * @property string $instruction_duration
 * @property string $class_notes
 * @property json $materials
 * @property string $assessment_notes
 * @property json $assessments
 * @property string $created_by
 * @property string $last_updated_by
 */
class InstructionRequestDetails extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'instruction_request_details';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'instruction_request_id',
        'librarian_id',
        'tasks_completed',
        'instruction_duration',
        'class_notes',
        'materials',
        'assessment_notes',
        'assessments',
        'created_by',
        'last_updated_by'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'instruction_request_id' => 'integer',
        'librarian_id' => 'integer',
        'tasks_completed' => 'array',
        'instruction_duration' => 'string',
        'class_notes' => 'string',
        'materials' => 'array',
        'assessment_notes' => 'string',
        'assessments' => 'array',
        'created_by' => 'string',
        'last_updated_by' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'instruction_request_id' => 'required|exists:instruction_requests,id',
        'librarian_id' => 'nullable|exists:users,id',
        'tasks_completed' => 'required|json',
        'materials' => 'nullable|json',
        'assessments' => 'nullable|json',
        'created_by' => 'required|string',
        'last_updated_by' => 'required|string'
    ];
}
