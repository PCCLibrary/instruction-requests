<?php

namespace App\Repositories;

use App\Models\InstructionRequestDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;

/**
 *
 */
class InstructionRequestDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected array $fieldSearchable = [
        'instruction_requests_id',
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

    /**
     * Update an existing record by its ID.
     *
     * @param array $data
     * @param int $id
     * @return Model
     */
    // In InstructionRequestDetailsRepository.php
    public function update(array $data, int $id): Model
    {
        try {
            // Using the correct ID - this is the primary key of the details record
            $model = $this->model->newQuery()->findOrFail($id);

            // Force type conversion for assigned_librarian_id if it exists
            if (isset($data['assigned_librarian_id'])) {
                // Convert to integer or null
                $data['assigned_librarian_id'] =
                    (is_null($data['assigned_librarian_id']) || $data['assigned_librarian_id'] === '')
                    ? null
                    : (int)$data['assigned_librarian_id'];
            }

            // Perform update using query builder to force it
            DB::table('instruction_request_details')
                ->where('id', $model->id)  // Use the model's ID to ensure we update the correct record
                ->update($data);

            // Get fresh model and log after state - query the database directly to verify the actual value
            $refreshed = $model->fresh();

            // Do a direct DB query to verify the actual value in the database
            $dbRecord = DB::table('instruction_request_details')
                ->where('id', $model->id)
                ->first();

            return $refreshed;
        } finally {
            DB::disableQueryLog();
        }
    }
}
