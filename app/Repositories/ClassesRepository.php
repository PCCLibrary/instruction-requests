<?php

namespace App\Repositories;

use App\Models\Classes;

class ClassesRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected array $fieldSearchable = [
        'department_code',
        'course_number',
        'course_name'
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
        return Classes::class;
    }
}
