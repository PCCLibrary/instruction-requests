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
        Log::info('Repository performing update', [
            'id' => $id,
            'update_data' => $data
        ]);

        DB::enableQueryLog();

        try {
            $model = $this->model->newQuery()->findOrFail($id);

            // Log before state
            Log::info('Before update state', [
                'assigned_librarian_id' => $model->assigned_librarian_id
            ]);

            // Perform update using query builder to force it
            DB::table('instruction_request_details')
                ->where('id', $id)
                ->update($data);

            // Get and log queries
            $queries = DB::getQueryLog();
            Log::info('Update queries executed:', [
                'queries' => $queries
            ]);

            // Get fresh model and log after state
            $refreshed = $model->fresh();
            Log::info('After update state', [
                'assigned_librarian_id' => $refreshed->assigned_librarian_id
            ]);

            return $refreshed;
        } finally {
            DB::disableQueryLog();
        }
    }
}
