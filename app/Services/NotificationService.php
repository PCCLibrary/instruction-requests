<?php

namespace App\Services;

use App\Models\Campus;
use App\Models\User;
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
    public function notifyLibrariansAboutRequest($instructionRequest)
    {
        try {
            // Retrieve the collection of librarian user models for the selected campus
            // $librarians = $this->getLibrariansByCampusId($instructionRequest->campus_id);

            $librarians = $this->getLibrariansByCampusId($instructionRequest->campus_id);

            // Generate the URL to view the instruction request
            $viewUrl = route('instructionRequests.edit', $instructionRequest->id);

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
     * Sends a confirmation notification to the instructor after they submit the instruction request form.
     *
     * @param InstructionRequest $instructionRequest The instruction request object.
     */
    public function confirmInstructionRequestFormSubmission(InstructionRequest $instructionRequest)
    {
        try {
            // Directly use the instructor's email from the instruction request
            $instructorEmail = $instructionRequest->email;

            // Construct a notifiable object on the fly for the instructor
            $notifiableInstructor = new \Illuminate\Notifications\AnonymousNotifiable;
            $notifiableInstructor->route('mail', $instructorEmail);

            // Send the notification
            $notifiableInstructor->notify(new InstructorNotification($instructionRequest));

            Log::info("Confirmation sent to instructor: {$instructorEmail}");
        } catch (\Exception $e) {
            Log::error("Failed to send form submission confirmation to instructor: {$e->getMessage()}");
        }
    }


}



