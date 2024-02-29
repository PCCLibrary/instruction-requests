<?php

namespace App\Repositories;

use App\Models\Classes;
use App\Repositories\BaseRepository;

/**
 * Class ClassesRepository
 * @package App\Repositories
 * @version February 26, 2024, 7:59 pm UTC
*/

class ClassesRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'department_code',
        'course_number',
        'course_name'
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
        return Classes::class;
    }
}
