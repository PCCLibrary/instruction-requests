<?php

namespace App\Repositories;

use App\Models\InstructionRequests;
use App\Repositories\BaseRepository;

/**
 * Class InstructionRequestsRepository
 * @package App\Repositories
 * @version February 2, 2024, 5:47 pm UTC
*/

class InstructionRequestsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'instructor_name',
        'display_name',
        'instructor_email',
        'instructor_phone',
        'instruction_type',
        'course_modality',
        'librarian',
        'class_location',
        'course_department',
        'course_number'
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return InstructionRequests::class;
    }
}
