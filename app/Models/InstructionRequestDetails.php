<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class InstructionRequestDetails
 * @package App\Models
 * @version February 26, 2024, 10:37 pm UTC
 *
 * @property unsignedInteger $instruction_request_id
 * @property unsignedBigInteger $assigned_librarian_id
 * @property string $instruction_duration
 * @property datetime $instruction_datetime
 * @property string $class_notes
 * @property bool $video,
 * @property bool $non_video,
 * @property bool $modified_tutorial,
 * @property bool $embedded,
 * @property bool $research_guide,
 * @property bool $handout,
 * @property bool $developed_assignment,
 * @property bool $other_materials,
 * @property string $other_describe,
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
        'assigned_librarian_id',
        'instruction_duration',
        'instruction_datetime',
        'class_notes',
        'video',
        'non_video',
        'modified_tutorial',
        'embedded',
        'research_guide',
        'handout',
        'developed_assignment',
        'other_materials',
        'other_describe',
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
        'assigned_librarian_id' => 'integer',
        'instruction_duration' => 'string',
        'instruction_datetime' => 'datetime',
        'class_notes' => 'string',
        'video' => 'boolean',
        'non_video' => 'boolean',
        'modified_tutorial' => 'boolean',
        'embedded' => 'boolean',
        'research_guide' => 'boolean',
        'handout' => 'boolean',
        'developed_assignment' => 'boolean',
        'other_materials' => 'boolean',
        'other_describe' => 'string',
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
        'assigned_librarian_id' => 'nullable|exists:users,id',
        'instruction_duration' => 'nullable|string',
        'instruction_datetime' => 'nullable|date',
        'class_notes' => 'nullable|string',
        'video' => 'boolean',
        'non_video' => 'boolean',
        'modified_tutorial' => 'boolean',
        'embedded' => 'boolean',
        'research_guide' => 'boolean',
        'handout' => 'boolean',
        'developed_assignment' => 'boolean',
        'other_materials' => 'boolean',
        'other_describe' => 'nullable|string',
        'materials' => 'nullable|array',
        'assessment_notes' => 'nullable|string',
        'assessments' => 'nullable|array',
        'created_by' => 'required|string',
        'last_updated_by' => 'required|string'
    ];

    /**
     * Get the validation rules that apply to the request.
     *
     * @param Request $request
     * @return array
     */
    public static function getRules(Request $request): array
    {
        return static::$rules;
    }
}
