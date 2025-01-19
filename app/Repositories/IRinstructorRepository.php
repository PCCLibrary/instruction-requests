<?php

namespace App\Repositories;

use App\Models\Instructor;

class IRinstructorRepository extends BaseRepository
{
    /**
     * Specify the model class that this repository works with.
     */
    public function model(): string
    {
        return Instructor::class; // Bind Instructor model
    }

    /**
     * Specify the searchable fields for the Instructor repository.
     */
    public function getFieldsSearchable(): array
    {
        return [
            'name',
            'email',
            'phone' // Add any other fields you want to make searchable
        ];
    }

//    /**
//     * Example of a custom repository method.
//     * Retrieve instructors filtered by a specific condition, if needed.
//     */
//    public function getInstructorsWithCustomCondition(array $conditions = [])
//    {
//        return $this->all($conditions); // Use the base `all()` functionality.
//    }
}
