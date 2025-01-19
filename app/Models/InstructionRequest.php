<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;
use LakM\Comments\Concerns\Commentable;
use LakM\Comments\Contracts\CommentableContract;

/**
 * Class InstructionRequest
 * Represents an instruction request in the system.
 *
 * @package App\Models
 */
class InstructionRequest extends Model implements HasMedia, CommentableContract
{
    use SoftDeletes, InteractsWithMedia, Commentable, HasFactory;

    /**
     * @var string Table name
     */
    protected $table = 'instruction_requests';

    /**
     * @var string[] Dates for soft deletes and casting
     */
    protected $dates = ['deleted_at'];

    /**
     * @var string[] Mass-assignable attributes
     */
    protected $fillable = [
        'instruction_type',
        'librarian_id',
        'campus_id',
        'class_id',
        'instructor_id',
        'department',
        'course_number',
        'course_crn',
        'number_of_students',
        'class_description',
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
        'status',
    ];

    /**
     * @var string[] Attribute casting
     */
    protected $casts = [
        'instruction_type' => 'string',
        'librarian_id' => 'integer',
        'campus_id' => 'integer',
        'class_id' => 'integer',
        'department' => 'string',
        'course_number' => 'string',
        'course_crn' => 'string',
        'number_of_students' => 'integer',
        'class_description' => 'string',
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
     * Get the detail associated with the instruction request.
     *
     * @return HasOne
     */
    public function detail(): HasOne
    {
        return $this->hasOne(InstructionRequestDetails::class);
    }

    /**
     * Get the instructor associated with the instruction request.
     *
     * @return BelongsTo
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Instructor::class, 'instructor_id');
    }

    /**
     * Get the campus associated with the instruction request.
     *
     * @return BelongsTo
     */
    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    /**
     * Get the librarian associated with the instruction request.
     *
     * @return BelongsTo
     */
    public function librarian(): BelongsTo
    {
        return $this->belongsTo(User::class, 'librarian_id');
    }

    /**
     * Get the class associated with the instruction request.
     *
     * @return BelongsTo
     */
    public function classes(): BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    /**
     * Validation rules for instruction requests.
     *
     * @param Request $request
     * @return array
     */
    public static function rules(Request $request): array
    {
        $rules = [
            'librarian_id' => 'nullable|exists:users,id',
            'campus_id' => 'required|exists:campuses,id',
            'instruction_type' => 'required|string',
            'department' => 'nullable|string',
            'course_number' => 'nullable|string',
            'course_crn' => 'nullable|string',
            'number_of_students' => 'nullable|integer',
            'class_syllabus.*' => 'nullable|file|mimes:txt,rtf,pdf,doc,docx|max:20480',
            'instructor_attachments.*' => 'nullable|file|mimes:txt,rtf,pdf,doc,docx|max:20480',
            'class_description' => 'nullable|string',
            'assignment_description' => 'nullable|string',
            'ada_provisions_needed' => 'boolean',
            'ada_provisions_description' => 'nullable|string',
            'preferred_datetime' => 'nullable|date',
            'alternate_datetime' => 'nullable|date',
            'duration' => 'nullable|string',
            'asynchronous_instruction_ready_date' => 'nullable|date',
            'extra_time_with_class' => 'nullable|string',
            'received_assignment' => 'boolean',
            'selected_topics' => 'boolean',
            'explored_background' => 'boolean',
            'written_draft' => 'boolean',
            'other_learning_outcome' => 'boolean',
            'other_learning_outcome_description' => 'nullable|string',
            'library_instruction_description' => 'nullable|string',
        ];

        // Apply conditional rules based on instruction type
        switch ($request->input('instruction_type')) {
            case 'on-campus':
            case 'remote':
                $rules['preferred_datetime'] = 'required|date';
                $rules['duration'] = 'required|string';
                break;
            case 'asynchronous':
                $rules['asynchronous_instruction_ready_date'] = 'required|date';
                break;
        }

        return $rules;
    }
}
