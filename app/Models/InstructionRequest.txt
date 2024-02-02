<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class InstructionRequest
 * @package App\Models
 * @version January 27, 2024, 12:42 am UTC
 *
 * @property string $instructor_name
 * @property string $students_refer_to_me_as
 * @property string $instructor_email
 * @property string $instructor_phone
 * @property string $instruction_type
 * @property string $course_modality
 * @property string $librarian_preference
 * @property string $class_location
 * @property string $course_department
 * @property string $course_number
 * @property string $course_crn
 * @property string $number_of_students
 * @property boolean $ada_provisions_needed
 * @property string $ada_provisions_description
 * @property date $preferred_datetime
 * @property date $alternate_datetime
 * @property string $duration
 * @property date $date_async_instruction_ready
 * @property string $extra_time_with_class
 * @property string $assignment_description_and_sample_topics
 * @property string $learning_outcomes_for_session
 * @property boolean $received_assignment
 * @property boolean $selected_topics
 * @property boolean $explored_background_topics
 * @property boolean $written_draft
 * @property boolean $other_preparation
 * @property string $other_preparation_specify
 * @property string $students_get_out_of_instruction
 * @property string $other_notes
 * @property string $librarian
 * @property boolean $created_video
 * @property boolean $created_non_video_tutorial
 * @property boolean $modified_existing_tutorial
 * @property boolean $embedded_online_classroom
 * @property boolean $developed_research_guide
 * @property boolean $developed_handout
 * @property boolean $developed_assignment_activity
 * @property boolean $other_development
 * @property string $other_development_describe
 * @property string $instruction_duration
 * @property string $class_notes
 * @property string $materials
 * @property string $assessment_notes
 * @property string $assessments
 * @property string $created_by
 * @property unsignedBigInteger $last_updated_by
 */
class InstructionRequest extends Model
{
    use SoftDeletes;

    public $table = 'instruction_requests';

    protected $dates = ['deleted_at', 'preferred_datetime', 'alternate_datetime', 'date_async_instruction_ready'];

    public $fillable = [
        'instructor_name',
        'students_refer_to_me_as',
        'instructor_email',
        'instructor_phone',
        'instruction_type',
        'course_modality',
        'librarian_preference',
        'class_location',
        'course_department',
        'course_number',
        'course_crn',
        'number_of_students',
        'ada_provisions_needed',
        'ada_provisions_description',
        'preferred_datetime',
        'alternate_datetime',
        'duration',
        'date_async_instruction_ready',
        'extra_time_with_class',
        'assignment_description_and_sample_topics',
        'learning_outcomes_for_session',
        'received_assignment',
        'selected_topics',
        'explored_background_topics',
        'written_draft',
        'other_preparation',
        'other_preparation_specify',
        'students_get_out_of_instruction',
        'other_notes',
        'librarian',
        'created_video',
        'created_non_video_tutorial',
        'modified_existing_tutorial',
        'embedded_online_classroom',
        'developed_research_guide',
        'developed_handout',
        'developed_assignment_activity',
        'other_development',
        'other_development_describe',
        'instruction_duration',
        'class_notes',
        'materials',
        'assessment_notes',
        'assessments',
        'created_by',
        'last_updated_by'
    ];

    protected $casts = [
        'id' => 'integer',
        'instructor_name' => 'string',
        'students_refer_to_me_as' => 'string',
        'instructor_email' => 'string',
        'instructor_phone' => 'string',
        'instruction_type' => 'string',
        'course_modality' => 'string',
        'librarian_preference' => 'string', // Note: This might be an integer if it references an ID
        'class_location' => 'integer',
        'course_department' => 'string',
        'course_number' => 'string',
        'course_crn' => 'string',
        'number_of_students' => 'string', // Consider changing to 'integer' if this field will always contain numeric values
        'ada_provisions_needed' => 'boolean',
        'ada_provisions_description' => 'string',
        'preferred_datetime' => 'date',
        'alternate_datetime' => 'date',
        'duration' => 'string',
        'date_async_instruction_ready' => 'date',
        'extra_time_with_class' => 'string',
        'assignment_description_and_sample_topics' => 'string',
        'learning_outcomes_for_session' => 'string',
        'received_assignment' => 'boolean',
        'selected_topics' => 'boolean',
        'explored_background_topics' => 'boolean',
        'written_draft' => 'boolean',
        'other_preparation' => 'boolean',
        'other_preparation_specify' => 'string',
        'students_get_out_of_instruction' => 'string',
        'other_notes' => 'string',
        'librarian' => 'integer', // Note: This might be an integer if it references an ID
        'created_video' => 'boolean',
        'created_non_video_tutorial' => 'boolean',
        'modified_existing_tutorial' => 'boolean',
        'embedded_online_classroom' => 'boolean',
        'developed_research_guide' => 'boolean',
        'developed_handout' => 'boolean',
        'developed_assignment_activity' => 'boolean',
        'other_development' => 'boolean',
        'other_development_describe' => 'string',
        'instruction_duration' => 'string',
        'class_notes' => 'string',
        'materials' => 'string',
        'assessment_notes' => 'string',
        'assessments' => 'string',
        'created_by' => 'string',
        'last_updated_by' => 'integer'
    ];


    public static $rules = [
        'instructor_name' => 'required|string|max:255',
        'students_refer_to_me_as' => 'nullable|string|max:255',
        'instructor_email' => 'required|string|email|max:255',
        'instructor_phone' => 'nullable|string|max:255',
        'instruction_type' => 'required|string|max:255',
        'course_modality' => 'required|string|max:255',
        'librarian_preference' => 'nullable|string|max:255', // If this is an ID reference, change to 'nullable|integer|exists:users,id'
        'class_location' => 'required|integer|exists:class_locations,id',
        'course_department' => 'required|string|max:255',
        'course_number' => 'required|string|max:255',
        'course_crn' => 'required|string|max:255',
        'number_of_students' => 'nullable|string|max:255', // If only numeric values are expected, consider 'nullable|numeric'
        'ada_provisions_needed' => 'required|boolean',
        'ada_provisions_description' => 'nullable|string',
        'preferred_datetime' => 'required|date',
        'alternate_datetime' => 'required|date',
        'duration' => 'required|string|max:255',
        'date_async_instruction_ready' => 'nullable|date',
        'extra_time_with_class' => 'nullable|string',
        'assignment_description_and_sample_topics' => 'nullable|string',
        'learning_outcomes_for_session' => 'nullable|string',
        'received_assignment' => 'nullable|boolean',
        'selected_topics' => 'nullable|boolean',
        'explored_background_topics' => 'nullable|boolean',
        'written_draft' => 'nullable|boolean',
        'other_preparation' => 'nullable|boolean',
        'other_preparation_specify' => 'nullable|string',
        'students_get_out_of_instruction' => 'nullable|string',
        'other_notes' => 'nullable|string',
        'librarian' => 'nullable|integer|exists:users,id', // Assuming this is a foreign key
        'created_video' => 'nullable|boolean',
        'created_non_video_tutorial' => 'nullable|boolean',
        'modified_existing_tutorial' => 'nullable|boolean',
        'embedded_online_classroom' => 'nullable|boolean',
        'developed_research_guide' => 'nullable|boolean',
        'developed_handout' => 'nullable|boolean',
        'developed_assignment_activity' => 'nullable|boolean',
        'other_development' => 'nullable|boolean',
        'other_development_describe' => 'nullable|string',
        'instruction_duration' => 'nullable|string|max:255',
        'class_notes' => 'nullable|string',
        'materials' => 'nullable|string',
        'assessment_notes' => 'nullable|string',
        'assessments' => 'nullable|string',
        'created_by' => 'nullable|string|max:255', // If this is an ID reference, adjust accordingly
        'last_updated_by' => 'nullable|integer|exists:users,id'
    ];


    // Define relationships:
    // public function classLocation() {
    //     return $this->belongsTo(ClassLocation::class, 'class_location');
    // }
    // Add other relationships here
}
