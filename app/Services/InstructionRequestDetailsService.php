<?php

namespace App\Services;

use App\Contracts\InstructionRequestDetailsServiceInterface;
use App\Models\InstructionRequestDetails;
use App\Repositories\InstructionRequestDetailsRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class InstructionRequestDetailsService implements InstructionRequestDetailsServiceInterface
{
    /**
     * @var InstructionRequestDetailsRepository
     */
    private InstructionRequestDetailsRepository $repository;

    /**
     * @param InstructionRequestDetailsRepository $repository
     */
    public function __construct(InstructionRequestDetailsRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param int $instructionRequestId
     * @return InstructionRequestDetails|null
     */
    public function getDetailsByInstructionRequestId(int $instructionRequestId): ?InstructionRequestDetails
    {
        $modelClass = $this->repository->model();
        return (new $modelClass)->where('instruction_requests_id', $instructionRequestId)->first();
    }

    /**
     * @param array $data
     * @param int $instructionRequestId
     * @return InstructionRequestDetails|null
     */
    public function updateInstructionRequestDetails(array $data, int $instructionRequestId): ?InstructionRequestDetails
    {
        Log::info('Starting details update', ['instruction_request_id' => $instructionRequestId]);
        Log::debug('Details update data:', $data);

        return DB::transaction(function () use ($data, $instructionRequestId) {
            // First, get the details record
            $details = $this->getDetailsByInstructionRequestId($instructionRequestId);

            if (!$details) {
                Log::warning('Details not found for update', ['instruction_request_id' => $instructionRequestId]);
                return null;
            }

            // Log the current state
            Log::info('Found details for update', [
                'details_id' => $details->id,
                'instruction_request_id' => $instructionRequestId,
                'current_assigned_librarian' => $details->assigned_librarian_id,
                'new_assigned_librarian' => $data['assigned_librarian_id'] ?? 'not provided'
            ]);

            // Enable query logging
            DB::enableQueryLog();

            try {
                // Clone to keep original state for logging
                $originalDetails = clone $details;
                
                // Debug the assigned_librarian_id specifically
                if (isset($data['assigned_librarian_id'])) {
                    Log::info('Updating assigned_librarian_id', [
                        'from' => $details->assigned_librarian_id,
                        'to' => $data['assigned_librarian_id'],
                        'data_type' => gettype($data['assigned_librarian_id'])
                    ]);
                }
                
                // Update using the repository
                $updatedDetails = $this->repository->update($data, $details->id);

                // Log the query that was executed
                $queries = DB::getQueryLog();
                Log::debug('SQL Query:', end($queries));

                // Check if the assigned_librarian_id actually changed
                $changed = $originalDetails->assigned_librarian_id != $updatedDetails->assigned_librarian_id;
                
                // Verify the update
                Log::info('Update verification', [
                    'details_id' => $updatedDetails->id,
                    'original_assigned_librarian' => $originalDetails->assigned_librarian_id,
                    'new_assigned_librarian' => $updatedDetails->assigned_librarian_id,
                    'assigned_librarian_changed' => $changed ? 'yes' : 'no'
                ]);

                return $updatedDetails;
            } finally {
                DB::disableQueryLog();
            }
        });
    }



    /**
     * @param int $id
     * @return InstructionRequestDetails|null
     */
    public function getInstructionRequestDetailsById(int $id): ?InstructionRequestDetails
    {
        /** @var InstructionRequestDetails|null */
        return $this->repository->find($id);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteInstructionRequestDetail(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $detail = $this->getInstructionRequestDetailsById($id);

            if (!$detail) {
                return false;
            }

            return $this->repository->delete($id);
        });
    }
}
