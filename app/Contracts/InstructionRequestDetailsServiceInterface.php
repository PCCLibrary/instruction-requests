<?php

namespace App\Contracts;

use App\Models\InstructionRequestDetails;

interface InstructionRequestDetailsServiceInterface
{
    /**
     * Get details by instruction request ID.
     *
     * @param int $instructionRequestId
     * @return InstructionRequestDetails|null
     */
    public function getDetailsByInstructionRequestId(int $instructionRequestId): ?InstructionRequestDetails;

    /**
     * Update instruction request details.
     *
     * @param array $data
     * @param int $instructionRequestId
     * @return InstructionRequestDetails|null
     */
    public function updateInstructionRequestDetails(array $data, int $instructionRequestId): ?InstructionRequestDetails;

    /**
     * Get details by ID.
     *
     * @param int $id
     * @return InstructionRequestDetails|null
     */
    public function getInstructionRequestDetailsById(int $id): ?InstructionRequestDetails;

    /**
     * Delete instruction request details.
     *
     * @param int $id
     * @return bool
     */
    public function deleteInstructionRequestDetail(int $id): bool;
}
