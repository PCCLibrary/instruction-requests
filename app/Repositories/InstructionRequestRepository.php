<?php

namespace App\Repositories;

use App\Models\InstructionRequest;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

/**
 * Class InstructionRequestRepository
 * @package App\Repositories
 * @version February 26, 2024, 5:47 pm UTC
 *
 * Repository pattern for accessing InstructionRequests data. It extends BaseRepository for common CRUD operations.
 */
class InstructionRequestRepository extends BaseRepository
{
    /**
     * @var array
     * Defines fields that can be used for searching or filtering InstructionRequests.
     */
    protected $fieldSearchable = [
        'instruction_type',
        'course_modality',
        'librarian_id',
        'campus_id',
        'department',
        'course_number',
        'course_crn',
        'number_of_students',
        'ada_provisions_needed',
        'preferred_datetime',
        'alternate_datetime',
        'duration',
        'learning_outcomes',
        'received_assignment',
        'selected_topics',
        'explored_background',
        'written_draft',
        'other_learning_outcome',
        'desired_student_outcomes',
        'genai_discussion_interest',
        'other_notes',
    ];

    /**
     * Return searchable fields
     *
     * @return array Array of fields that can be searched upon.
     */
    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     * Specifies the model class name that this repository deals with.
     *
     * @return string The model class name.
     **/
    public function model(): string
    {
        return InstructionRequest::class;
    }
}
