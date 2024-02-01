<?php

namespace App\Repositories;

use App\Models\request;
use App\Repositories\BaseRepository;

/**
 * Class requestRepository
 * @package App\Repositories
 * @version January 27, 2024, 12:42 am UTC
*/

class requestRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'classname',
        'description'
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
        return request::class;
    }
}
