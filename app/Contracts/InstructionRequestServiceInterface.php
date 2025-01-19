<?php

namespace App\Contracts;

use App\Models\InstructionRequest;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;

interface InstructionRequestServiceInterface
{
    /**
     * Create a new instruction request with associated details and files.
     *
     * @param array $data
     * @param Request $request
     * @return InstructionRequest
     */
    public function createNewInstructionRequest(array $data, Request $request): InstructionRequest;

    /**
     * Update an existing instruction request and its details.
     *
     * @param array $data
     * @param int $id
     * @return InstructionRequest
     * @throws \Exception When instruction request not found
     */
    public function updateInstructionRequest(array $data, int $id): InstructionRequest;

    /**
     * Find an instruction request by ID.
     *
     * @param int $id
     * @return InstructionRequest|null
     */
    public function findInstructionRequestById(int $id): ?InstructionRequest;

    /**
     * Delete an instruction request.
     *
     * @param int $id
     * @return bool
     */
    public function deleteInstructionRequest(int $id): bool;

    /**
     * Accept an instruction request.
     *
     * @param int $id
     * @param int $userId
     * @return void
     */
    public function acceptRequest(int $id, int $userId): void;

    /**
     * Reject an instruction request.
     *
     * @param int $id
     * @return void
     */
    public function rejectRequest(int $id): void;

    /**
     * Get requests by status.
     *
     * @param string $status
     * @param int|null $quantity
     * @return Collection
     */
    public function getRequestsByStatus(string $status, ?int $quantity = null): Collection;

    /**
     * Handle file uploads for an instruction request.
     *
     * @param Request $request
     * @param string $fieldName
     * @param string $collectionName
     * @param InstructionRequest $instructionRequest
     * @return void
     */
    public function handleFileUploads(
        Request $request,
        string $fieldName,
        string $collectionName,
        InstructionRequest $instructionRequest
    ): void;
}
