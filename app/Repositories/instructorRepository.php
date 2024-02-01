<?php

namespace App\Repositories;

use App\Models\instructor;
use App\Repositories\BaseRepository;

/**
 * Class instructorRepository
 * @package App\Repositories
 * @version January 26, 2024, 11:59 pm UTC
*/

class instructorRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'display_name',
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
     **/
    public function model()
    {
        return instructor::class;
    }
}