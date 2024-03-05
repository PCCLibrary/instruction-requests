<?php

namespace App\Repositories;

use App\Models\InstructionRequestDetails;
use App\Repositories\BaseRepository;

/**
 * Class InstructionRequestDetailsRepository
 * @package App\Repositories
 * @version February 26, 2024, 10:37 pm UTC
*/

class InstructionRequestDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'librarian_id',
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
        return InstructionRequestDetails::class;
    }
}
