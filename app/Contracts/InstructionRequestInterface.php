<?php

namespace App\Contracts;

use App\Models\InstructionRequest;

interface InstructionRequestInterface
{
    /**
     * Create a new instruction request and associated entities like Instructor, classes, and instruction request details.
     *
     * @param array $data Data for creating the instruction request and associated entities.
     * @return InstructionRequest Newly created InstructionRequest object.
     */
    public function createNewInstructionRequest(array $data): InstructionRequest;

    /**
     * Find an instruction request by its ID.
     *
     * @param int $id The ID of the instruction request to find.
     * @return InstructionRequest|null The found InstructionRequest object, or null if not found.
     */
    public function findInstructionRequestById(int $id): ?InstructionRequest;

    /**
     * Update an instruction request and its associated entities by ID.
     *
     * @param int $id The ID of the instruction request to update.
     * @param array $data Data to update the instruction request and associated entities.
     * @return InstructionRequest Updated InstructionRequest object.
     */
    public function updateInstructionRequest(int $id, array $data): InstructionRequest;

    /**
     * Delete an instruction request by its ID.
     *
     * @param int $id The ID of the instruction request to delete.
     * @return bool True if the instruction request was successfully deleted, false otherwise.
     */
    public function deleteInstructionRequest(int $id): bool;
}
