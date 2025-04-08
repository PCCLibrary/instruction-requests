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
        Log::info('REPOSITORY ENTRY: update method called', [
            'id' => $id,
            'has_assigned_librarian' => isset($data['assigned_librarian_id']) ? 'YES' : 'NO',
            'assigned_librarian_id' => $data['assigned_librarian_id'] ?? 'NOT PROVIDED'
        ]);
        
        // Complete data dump for debugging
        Log::debug('REPOSITORY UPDATE COMPLETE DATA DUMP', $data);

        DB::enableQueryLog();

        try {
            // Using the correct ID - this is the primary key of the details record
            $model = $this->model->newQuery()->findOrFail($id);

            // Log before state
            Log::info('Before update state', [
                'assigned_librarian_id' => $model->assigned_librarian_id
            ]);
            
            // Force type conversion for assigned_librarian_id if it exists
            if (isset($data['assigned_librarian_id'])) {
                // Convert to integer or null
                $data['assigned_librarian_id'] = 
                    (is_null($data['assigned_librarian_id']) || $data['assigned_librarian_id'] === '')
                    ? null
                    : (int)$data['assigned_librarian_id'];
                
                Log::info('Converted assigned_librarian_id', [
                    'original' => $model->assigned_librarian_id,
                    'new_value' => $data['assigned_librarian_id'], 
                    'type' => gettype($data['assigned_librarian_id'])
                ]);
            }

            // Perform update using query builder to force it
            DB::table('instruction_request_details')
                ->where('id', $model->id)  // Use the model's ID to ensure we update the correct record
                ->update($data);

            // Get and log queries
            $queries = DB::getQueryLog();
            Log::info('Update queries executed:', [
                'queries' => $queries
            ]);

            // Get fresh model and log after state - query the database directly to verify the actual value
            $refreshed = $model->fresh();
            
            // Do a direct DB query to verify the actual value in the database
            $dbRecord = DB::table('instruction_request_details')
                ->where('id', $model->id)
                ->first();
                
            Log::info('AFTER UPDATE STATE - DIRECT DATABASE CHECK', [
                'model_assigned_librarian_id' => $refreshed->assigned_librarian_id,
                'db_query_assigned_librarian_id' => $dbRecord ? $dbRecord->assigned_librarian_id : 'DB RECORD NOT FOUND',
                'instruction_datetime' => $refreshed->instruction_datetime,
                'instruction_duration' => $refreshed->instruction_duration
            ]);

            return $refreshed;
        } finally {
            DB::disableQueryLog();
        }
    }
}
