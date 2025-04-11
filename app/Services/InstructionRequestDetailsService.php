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
        return DB::transaction(function () use ($data, $instructionRequestId) {
            $details = $this->getDetailsByInstructionRequestId($instructionRequestId);

            if (!$details) {
                return null;
            }

            try {
                $updatedDetails = $this->repository->update($data, $details->id);
                return $updatedDetails;
            } finally {
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
