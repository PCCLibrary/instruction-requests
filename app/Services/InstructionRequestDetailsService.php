<?php

namespace App\Services;

use App\Contracts\InstructionRequestDetailsServiceInterface;
use App\Models\InstructionRequestDetails;
use App\Repositories\InstructionRequestDetailsRepository;
use Illuminate\Support\Facades\DB;

class InstructionRequestDetailsService implements InstructionRequestDetailsServiceInterface
{
    private InstructionRequestDetailsRepository $repository;

    public function __construct(InstructionRequestDetailsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getDetailsByInstructionRequestId(int $instructionRequestId): ?InstructionRequestDetails
    {
        return $this->repository->model()::where('instruction_request_id', $instructionRequestId)->first();
    }

    public function updateInstructionRequestDetails(array $data, int $instructionRequestId): ?InstructionRequestDetails
    {
        return DB::transaction(function () use ($data, $instructionRequestId) {
            $details = $this->getDetailsByInstructionRequestId($instructionRequestId);

            if (!$details) {
                return null;
            }

            $data['last_updated_by'] = auth()->user()?->display_name ?? 'System';
            $this->repository->update($data, $details->id);

            return $details->fresh();
        });
    }

    public function getInstructionRequestDetailsById(int $id): ?InstructionRequestDetails
    {
        return $this->repository->find($id);
    }

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
