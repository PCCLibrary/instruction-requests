<?php

namespace App\Repositories;

use App\Models\Instructor;
use App\Repositories\BaseRepository;

/**
 * Class IRinstructorRepository
 * @package App\Repositories
 * @version January 26, 2024, 11:59 pm UTC
*/

class IRinstructorRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'display_name',
        'pronouns',
        'email',
        'phone'
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
     * @return Instructor
     **/
    public function model()
    {
        return Instructor::class;
    }
}
