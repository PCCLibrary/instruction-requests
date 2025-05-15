<?php

namespace App\Contracts;

use App\Models\InstructionRequests;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;

interface InstructionRequestServiceInterface
{
    /**
     * Create a new instruction request with associated details and files.
     *
     * @param array $data
     * @param Request $request
     * @return InstructionRequests
     */
    public function createNewInstructionRequest(array $data, Request $request): InstructionRequests;

    /**
     * Lock an instruction request for a specific user.
     *
     * @param int $id
     * @param int|null $userId
     * @return InstructionRequests|null
     * @throws \Exception
     */
    public function lockRequest(int $id, ?int $userId = null): ?InstructionRequests;

    /**
     * Unlock an instruction request.
     *
     * @param int $id
     * @param bool $force Whether to force unlock (admin only)
     * @return InstructionRequests|null
     * @throws \Exception
     */
    public function unlockRequest(int $id, bool $force = false): ?InstructionRequests;

    /**
     * Update an existing instruction request and its details.
     *
     * @param array $data
     * @param int $id
     * @return InstructionRequests
     * @throws \Exception When instruction request not found
     */
    public function updateInstructionRequest(array $data, int $id): InstructionRequests;

    /**
     * Find an instruction request by ID.
     *
     * @param int $id
     * @return InstructionRequests|null
     */
    public function findInstructionRequestById(int $id): ?InstructionRequests;

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
     * Get requests by instructor.
     *
     * @param int $instructorId
     * @param int|null $quantity
     * @return Collection
     */
    public function getRequestsByInstructor(int $instructorId, ?int $quantity = null): Collection;

    /**
     * Handle file uploads for an instruction request.
     *
     * @param Request $request
     * @param string $fieldName
     * @param string $collectionName
     * @param InstructionRequests $instructionRequest
     * @return void
     */
    public function handleFileUploads(
        Request $request,
        string $fieldName,
        string $collectionName,
        InstructionRequests $instructionRequest
    ): void;
}
