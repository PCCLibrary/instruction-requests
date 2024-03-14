<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\InstructionRequestDetails;
use Illuminate\Http\Request;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;

/**
 * Class InstructionRequest
 * @package App\Models
 * @version February 26, 2024
 *
 * @property Instructor $Instructor
 * @property Campus $campus
 * @property User $librarianUser
 * @property string $instruction_type
 * @property string $course_modality
 * @property int $librarian_id
 * @property int $campus_id
 * @property string $department
 * @property string $course_number
 * @property string $course_crn
 * @property integer $number_of_students
 * @property array $class_syllabus,
 * @property array $instructor_attachments,
 * @property string $assignment_description,
 * @property boolean $ada_provisions_needed
 * @property string $ada_provisions_description
 * @property datetime $preferred_datetime
 * @property datetime $alternate_datetime
 * @property string $duration
 * @property datetime $asynchronous_instruction_ready_date
 * @property string $extra_time_with_class
 * @property string $learning_outcomes
 * @property boolean $received_assignment
 * @property boolean $selected_topics
 * @property boolean $explored_background
 * @property boolean $written_draft
 * @property boolean $other_learning_outcome
 * @property string $other_learning_outcome_description
 * @property string $desired_student_outcomes
 * @property string $genai_discussion_interest
 * @property string $other_notes
 * @property string $status

 */
class InstructionRequest extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    /**
     * @var string
     */
    public $table = 'instruction_requests';

    /**
     * @var string[]
     */
    protected $dates = ['deleted_at'];

    /**
     * @var string[]
     */
    protected $fillable = [
//        'instruction_request_id',
        'instruction_type',
        'course_modality',
        'librarian_id',
        'campus_id',
        'class_id',
        'instructor_id',
        'department',
        'course_number',
        'course_crn',
        'number_of_students',
//        'class_syllabus',
//        'instructor_attachments',
        'assignment_description',
        'ada_provisions_needed',
        'ada_provisions_description',
        'preferred_datetime',
        'alternate_datetime',
        'duration',
        'asynchronous_instruction_ready_date',
        'extra_time_with_class',
        'learning_outcomes',
        'received_assignment',
        'selected_topics',
        'explored_background',
        'written_draft',
        'other_learning_outcome',
        'other_learning_outcome_description',
        'library_instruction_description',
        'desired_student_outcomes',
        'genai_discussion_interest',
        'other_notes',
        'status'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'id' => 'integer',
        'instruction_type' => 'string',
        'course_modality' => 'string',
        'librarian_id' => 'integer',
        'campus_id' => 'integer',
        'class_id' => 'integer',
        'department' => 'string',
        'course_number' => 'string',
        'course_crn' => 'string',
        'number_of_students' => 'integer',
//        'class_syllabus' => 'array',
//        'instructor_attachments' => 'array',
        'assignment_description' => 'string',
        'ada_provisions_needed' => 'boolean',
        'ada_provisions_description' => 'string',
        'preferred_datetime' => 'datetime',
        'alternate_datetime' => 'datetime',
        'duration' => 'string',
        'asynchronous_instruction_ready_date' => 'date',
        'extra_time_with_class' => 'string',
        'learning_outcomes' => 'string',
        'received_assignment' => 'boolean',
        'selected_topics' => 'boolean',
        'explored_background' => 'boolean',
        'written_draft' => 'boolean',
        'other_learning_outcome' => 'boolean',
        'other_learning_outcome_description' => 'string',
        'library_instruction_description' => 'string',
        'desired_student_outcomes' => 'string',
        'genai_discussion_interest' => 'string',
        'other_notes' => 'string',
        'status' => 'string',
    ];

    // Define relationships
    /**
     * Get the instruction request detail associated with the instruction request
     *
     * @return HasOne
     */
    public function detail(): HasOne
    {
        return $this->hasOne(InstructionRequestDetails::class);
    }


    /**
     * @return BelongsTo
     */
    public function instructor()
    {
        return $this->belongsTo(Instructor::class, 'instructor_id');
    }

    /**
     * @return BelongsTo
     */
    public function campus()
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    /**
     * @return BelongsTo
     */
    public function librarian()
    {
        return $this->belongsTo(User::class, 'librarian_id');
    }

    /**
     * @return BelongsTo
     */
    public function classes()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
        $rules = [
            'librarian_id' => 'required|exists:users,id',
            'campus_id' => 'required|exists:campuses,id',
            'instruction_type' => 'required|string',
            'course_modality' => 'required|string',
            'department' => 'nullable|string',
            'course_number' => 'nullable|string',
            'course_crn' => 'nullable|string',
            'number_of_students' => 'nullable|integer',
//            'class_syllabus' => 'nullable|array',
//            'instructor_attachments' => 'nullable|array',
            'class_syllabus.*' => 'nullable|file|mimes:pdf,doc,docx|max:20480', // Example: PDF, Word document
            'instructor_attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:20480', // Example: Images, PDF, Word document
            'assignment_description' => 'nullable|string',
            'ada_provisions_needed' => 'boolean',
            'ada_provisions_description' => 'nullable|string',
            'preferred_datetime' => 'nullable|date',
            'alternate_datetime' => 'nullable|date',
            'duration' => 'nullable|string',
            'asynchronous_instruction_ready_date' => 'nullable|date',
            'need_extra_time' => 'boolean',
            'extra_time_with_class' => 'nullable|string',
            'received_assignment' => 'boolean',
            'selected_topics' => 'boolean',
            'explored_background' => 'boolean',
            'written_draft' => 'boolean',
            'other_learning_outcome' => 'boolean',
            'other_learning_outcome_description' => 'nullable|string',
            'library_instruction_description' => 'nullable|string',

        ];

        // Additional rules for creation
        if ($request->method() === 'POST') {
            $rules += [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'display_name' => 'nullable|string|max:255',
                'pronouns' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:255',
            ];
        }

        // Additional rules for edit to ensure relationship integrity
        if ($request->method() === 'PUT' || $request->method() === 'PATCH') {
            $rules += [
                'librarian_id' => 'required|exists:users,id',
                'campus_id' => 'required|exists:campuses,id',
                'instructor_id' => 'required|exists:instructors,id',
                'instruction_request_id' => 'required|exists:instruction_requests,id',
            ];

            // Merge detail rules for the edit operation
            $rules = self::mergeDetailRules($rules, $request);
        }

        return $rules;
    }

    /**
     * Merge the detail rules for the edit operation.
     *
     * @param array $rules
     * @param Request $request
     * @return array
     */
    private static function mergeDetailRules(array $rules, Request $request): array
    {
        // Create an instance of InstructionRequestDetails
        $instructionRequestDetails = new InstructionRequestDetails;

        // Get the detail rules
        $detailRules = $instructionRequestDetails::getRules($request);

        // Merge the detail rules
        return array_merge($rules, $detailRules);
    }

}
