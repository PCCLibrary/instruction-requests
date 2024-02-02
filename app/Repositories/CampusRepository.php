<?php

namespace App\Repositories;

use App\Models\Campus;
use App\Repositories\BaseRepository;

/**
 * Class CampusRepository
 * @package App\Repositories
 * @version February 1, 2024, 11:27 pm UTC
*/

class CampusRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'code'
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
        return Campus::class;
    }
}
