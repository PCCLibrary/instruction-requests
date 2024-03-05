<?php

namespace App\Contracts;

use App\Models\InstructionRequestDetails;

/**
 * Service interface for InstructionRequestDetails
 */
interface InstructionRequestDetailsServiceInterface
{
    /**
     * Find an InstructionRequestDetail by the id of the InstructionRequest
     * @param int $instructionRequestId
     * @return InstructionRequestDetails|null
     */
    public function getDetailsByInstructionRequestId(int $instructionRequestId): InstructionRequestDetails;

    /**
     * Update the selected InstructionRequestDetail
     *
     * @param array $data
     * @param int $instructionRequestId
     * @return InstructionRequestDetails|null
     */
    public function updateInstructionRequestDetails(array $data, int $instructionRequestId) : InstructionRequestDetails;

    /**
     * Get an InstructionRequestDetails by its ID.
     *
     * @param int $id
     * @return InstructionRequestDetails|null
     */
    public function getInstructionRequestDetailsById(int $id) : InstructionRequestDetails;


    /**
     * Delete an instruction request detail by its ID.
     *
     * @param int $id The ID of the instruction request detail to delete.
     * @return bool True if the instruction request was successfully deleted, false otherwise.
     */
    public function deleteInstructionRequestDetail(int $id): bool;



}
