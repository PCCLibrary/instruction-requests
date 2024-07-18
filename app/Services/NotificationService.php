<?php

namespace App\Services;

use App\Models\Campus;
use App\Models\User;
use App\Models\Instructor;
use App\Notifications\LibrarianNotification;
use App\Notifications\InstructorNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use App\Models\InstructionRequest;
use App\Services\InstructionRequestService;

/**
 * Service class for handling notification logic.
 */
class NotificationService
{
    protected $instructionRequestService;

    /**
     * Constructor to initialize services.
     *
     * @param InstructionRequestService $instructionRequestService The instruction request service.
     */
    public function __construct(InstructionRequestService $instructionRequestService)
    {
        $this->instructionRequestService = $instructionRequestService;
    }

    /**
     * Notify based on the status of the instruction request.
     *
     * @param InstructionRequest $instructionRequest The instruction request object.
     * @return void
     */
    public function notifyBasedOnStatus(InstructionRequest $instructionRequest)
    {
        $status = $instructionRequest->status;
        $instructor = $this->getInstructorById($instructionRequest->instructor_id);

        if ($instructor) {
            switch ($status) {
                case 'received':
                    $this->notifyInstructor($instructionRequest, 'Instruction Request Received');
                    $this->notifyLibrarians($instructionRequest, 'New Instruction Request Submitted');
                    break;
                case 'assigned':
                    $assignedLibrarian = $this->getAssignedLibrarian($instructionRequest->id);
                    if ($assignedLibrarian) {
                        $this->sendNotification($assignedLibrarian, $instructionRequest, 'An instruction request has been assigned to you');
                    }
                    break;
                case 'rejected':
                    $this->notifyLibrarians($instructionRequest, 'Instruction Request Rejected');
                    break;
                case 'accepted':
                    $this->notifyLibrarians($instructionRequest, 'Instruction Request Accepted');
                    $assignedLibrarian = $this->getAssignedLibrarian($instructionRequest->id);
                    if ($assignedLibrarian) {
                        $this->sendNotification($assignedLibrarian, $instructionRequest, 'Instruction Request Accepted');
                    }
                    break;
            }
        }
    }

    /**
     * Send a notification to a user (librarian).
     *
     * @param User $user The user to notify.
     * @param InstructionRequest $instructionRequest The instruction request object.
     * @param string $subject The email subject.
     * @return void
     */
    protected function sendNotification(User $user, InstructionRequest $instructionRequest, string $subject)
    {
        try {
            $user->notify(new LibrarianNotification($instructionRequest, $subject));
            Log::info("Notification sent to user: {$user->email}");
        } catch (\Exception $e) {
            Log::error("Failed to send notification: {$e->getMessage()}");
        }
    }

    /**
     * Notify an instructor.
     *
     * @param InstructionRequest $instructionRequest The instruction request object.
     * @param string $subject The email subject.
     * @return void
     */
    protected function notifyInstructor(InstructionRequest $instructionRequest, string $subject)
    {
        try {
            $instructor = $this->getInstructorById($instructionRequest->instructor_id);
            if ($instructor) {
                $instructor->notify(new InstructorNotification($instructionRequest, $subject));
                Log::info("Notification sent to instructor: {$instructor->email}");
            } else {
                Log::warning("Instructor notification not sent due to missing instructor information for request ID: {$instructionRequest->id}");
            }
        } catch (\Exception $e) {
            Log::error("Failed to send notification to instructor: {$e->getMessage()}");
        }
    }

    /**
     * Notify librarians associated with the campus of an instruction request.
     *
     * @param InstructionRequest $instructionRequest The instruction request object.
     * @param string $subject The email subject.
     * @return void
     */
    protected function notifyLibrarians(InstructionRequest $instructionRequest, string $subject)
    {
        try {
            $librarians = $this->getLibrariansByCampusId($instructionRequest->campus_id);

            foreach ($librarians as $librarian) {
                $this->sendNotification($librarian, $instructionRequest, $subject);
            }
        } catch (\Exception $e) {
            Log::error("Failed to notify librarians: {$e->getMessage()}");
        }
    }

    /**
     * Retrieves a collection of user models for librarians associated with a given campus.
     *
     * @param int $campusId The campus ID.
     * @return Collection A collection of user models.
     */
    protected function getLibrariansByCampusId(int $campusId): Collection
    {
        $campus = Campus::find($campusId);

        if (!empty($campus->librarian_ids)) {
            // Decode the JSON array of librarian IDs
            $librarianIds = json_decode($campus->librarian_ids, true);

            // Retrieve user models for all librarians using the IDs
            $librarians = User::whereIn('id', $librarianIds)->get();

            Log::debug('Librarians to notify: ' . $librarians->pluck('email'));

            return $librarians;
        }

        return collect();
    }

    /**
     * Retrieves the instructor model by ID.
     *
     * @param int $instructorId The instructor ID.
     * @return Instructor|null The instructor model or null if not found.
     */
    protected function getInstructorById(int $instructorId): ?Instructor
    {
        try {
            $instructor = Instructor::find($instructorId);

            if (!$instructor) {
                Log::warning("Instructor not found with ID: {$instructorId}");
                return null;
            }

            return $instructor;
        } catch (\Exception $e) {
            Log::error("Failed to retrieve instructor by ID: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Retrieves the assigned librarian for the instruction request using InstructionRequestService.
     *
     * @param int $requestId The instruction request ID.
     * @return User|null The assigned librarian or null if not found.
     */
    protected function getAssignedLibrarian(int $requestId): ?User
    {
        $instructionRequest = $this->instructionRequestService->findInstructionRequestById($requestId);
        if ($instructionRequest && $instructionRequest->detail) {
            return User::find($instructionRequest->detail->assigned_librarian_id);
        }
        return null;
    }
}
