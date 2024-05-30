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

class NotificationService
{

    /**
     * Notifies librarians associated with the campus of a new instruction request.
     *
     * @param InstructionRequest $instructionRequest The instruction request object.
     */
    public function librarianNotification($instructionRequest) : InstructionRequest
    {
        try {

            $librarians = $this->getLibrariansByCampusId($instructionRequest->campus_id);

            // Generate the URL to view the instruction request
            $viewUrl = route('instructionRequests.show', $instructionRequest->id);

            // Notify each librarian about the new instruction request
            foreach ($librarians as $librarian) {
                $librarian->notify(new LibrarianNotification($instructionRequest, $viewUrl));

                Log::info("Notification sent to librarian: {$librarian->email}");
            }
        } catch (\Exception $e) {
            Log::error("Failed to notify librarians about new instruction request: {$e->getMessage()}");
        }
    }

    /**
     * Retrieves a collection of user models for librarians associated with a given campus.
     *
     * @param int $campusId The campus ID.
     * @return Collection A collection of user models.
     */
    protected function getLibrariansByCampusId(int $campusId) : Collection
    {
        $campus = Campus::find($campusId);

        if (!empty($campus->librarian_ids)) {
            // Decode the JSON array of librarian IDs
            $librarianIds = json_decode($campus->librarian_ids, true);

            // Retrieve user models for all librarians using the IDs
            $librarians = User::whereIn('id', $librarianIds)->get();

            Log::debug('Librarians to notify: '. $librarians->pluck('email'));

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
     * Sends a confirmation notification to the instructor after they submit the instruction request form.
     *
     * @param InstructionRequest $instructionRequest The instruction request object.
     */
    public function newRequestConfirmation(InstructionRequest $instructionRequest)
    {
        try {
            // Retrieve the instructor model using the instructor_id from the instruction request
            $instructor = $this->getInstructorById($instructionRequest->instructor_id);

            if ($instructor) {
                $instructor->notify(new InstructorNotification($instructionRequest));
                Log::info("Confirmation sent to instructor: {$instructor->email}");
            } else {
                Log::warning("Instructor notification not sent due to missing instructor information for request ID: {$instructionRequest->id}");
            }
        } catch (\Exception $e) {
            Log::error("Failed to send form submission confirmation to instructor: {$e->getMessage()}");
        }
    }

}



