<?php

namespace App\Repositories;

use App\Models\InstructionRequestDetails;

class InstructionRequestDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected array $fieldSearchable = [
        'instruction_request_id',
        'assigned_librarian_id',
        'instruction_duration',
        'instruction_datetime',
        'created_by',
        'last_updated_by'
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
        return InstructionRequestDetails::class;
    }
}
