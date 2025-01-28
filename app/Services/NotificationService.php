<?php

namespace App\Services;

use App\Models\Campus;
use App\Models\User;
use App\Models\InstructionRequests;
use App\Notifications\LibrarianNotification;
use App\Notifications\InstructorNotification;
use App\Notifications\StatusChangeNotification;
use App\Enums\InstructionRequestStatus;
use Illuminate\Support\Facades\Log;

/**
 * Service for handling notification logic
 */
class NotificationService
{
    /**
     * Send notifications based on request status
     */
    public function notifyBasedOnStatus(InstructionRequests $request): void
    {
        try {
            $status = InstructionRequestStatus::fromString($request->status);

            match($status) {
                InstructionRequestStatus::RECEIVED => $this->handleReceivedStatus($request),
                InstructionRequestStatus::ASSIGNED => $this->handleAssignedStatus($request),
                InstructionRequestStatus::REJECTED => $this->notifyLibrarians($request, 'Request Rejected'),
                default => null
            };

        } catch (\Exception $e) {
            Log::error('Notification failed', [
                'request_id' => $request->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle received status notifications
     */
    private function handleReceivedStatus(InstructionRequests $request): void
    {
        $this->notifyInstructor($request, 'Library Instruction Request Received');
        $this->notifyLibrarians($request, 'New Library Instruction Request');
    }

    /**
     * Handle assigned status notifications
     */
    private function handleAssignedStatus(InstructionRequests $request): void
    {
        $librarian = User::find($request->detail?->assigned_librarian_id);
        if ($librarian) {
            $librarian->notify(
                StatusChangeNotification::fromRequest($request, 'Request Assigned to You')
            );
        }
    }

    /**
     * Notify instructor about their request
     */
    private function notifyInstructor(InstructionRequests $request, string $subject): void
    {
        try {
            $instructor = $request->instructor;
            if ($instructor) {
                $instructor->notify(
                    InstructorNotification::fromRequest($request, $subject)
                );
                Log::info("Instructor notified", ['email' => $instructor->email]);
            }
        } catch (\Exception $e) {
            Log::error("Instructor notification failed", [
                'request_id' => $request->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Notify librarians about a request
     */
    private function notifyLibrarians(InstructionRequests $request, string $subject): void
    {
        try {
            $campus = Campus::find($request->campus_id);
            if ($campus && !empty($campus->librarian_ids)) {
                $librarianIds = is_string($campus->librarian_ids)
                    ? json_decode($campus->librarian_ids, true)
                    : $campus->librarian_ids;

                User::whereIn('id', $librarianIds)->each(function($librarian) use ($request, $subject) {
                    $librarian->notify(
                        LibrarianNotification::fromRequest($request, $subject)
                    );
                });
            }
        } catch (\Exception $e) {
            Log::error("Librarian notification failed", [
                'request_id' => $request->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
