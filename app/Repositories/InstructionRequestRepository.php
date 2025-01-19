<?php

namespace App\Repositories;

use App\Models\InstructionRequest;

class InstructionRequestRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected array $fieldSearchable = [
        'instruction_type',
        'librarian_id',
        'campus_id',
        'class_id',
        'instructor_id',
        'department',
        'course_number',
        'course_crn',
        'status'
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     *
     * @return string
     */
    public function model(): string
    {
        return InstructionRequest::class;
    }
}
