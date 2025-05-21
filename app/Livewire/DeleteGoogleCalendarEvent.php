<?php

namespace App\Livewire;

use App\Models\InstructionRequests;
use App\Models\GoogleCalendarEvent;
use App\Services\CalendarService;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use function Masmerise\Toaster\toast;

class DeleteGoogleCalendarEvent extends Component
{
    public int $requestId;
    public bool $isProcessing = false;
    public ?string $errorMessage = null;

    public function mount(int $requestId)
    {
        $this->requestId = $requestId;
    }

    /**
     * Delete the Google Calendar event associated with an instruction request
     */
    public function deleteEvent()
    {
        $this->isProcessing = true;
        $this->errorMessage = null;

        try {
            // Find the instruction request and ensure it has a calendar event
            $instructionRequest = InstructionRequests::with('googleCalendarEvent')
                ->findOrFail($this->requestId);

            if (!$instructionRequest->googleCalendarEvent) {
                $this->errorMessage = 'No calendar event found for this instruction request.';

                // Use toast helper for error
                toast()
                    ->error($this->errorMessage)
                    ->autoDismiss(10);

                $this->isProcessing = false;
                return;
            }

            // Store event ID for verification after deletion
            $eventId = $instructionRequest->googleCalendarEvent->id;

            // Use the calendar service to delete the event
            $calendarService = app(CalendarService::class);
            $result = $calendarService->deleteEvent($instructionRequest->googleCalendarEvent);

            // Handle successful deletion
            if ($result['success']) {
                $message = implode(' ', $result['messages']);

                // Add a short delay to ensure DB changes are visible before redirect
                sleep(1);

                // Verify that record was actually deleted
                $verifyDeleted = is_null(GoogleCalendarEvent::find($eventId));

                // Log deletion status
                Log::info('Calendar event deletion completed', [
                    'request_id' => $this->requestId,
                    'success' => $result['success'],
                    'record_deleted' => $verifyDeleted ? 'Yes' : 'No'
                ]);

                // Dispatch event to close the modal
                $this->dispatch('deletion-completed');

                // Short delay to ensure modal is closed before redirect
                usleep(300000); // 300ms

                // Flash a success toast that will be shown after redirect
                toast()
                    ->success($message)
                    ->autoDismiss(5);

                // Make sure we're returning the redirect with a hash parameter to force reload
                return $this->redirect(
                    route('instructionRequests.edit', [
                        'id' => $result['instruction_request_id'],
                        'calendar_deleted' => 'true',
                        '_' => time() // Add timestamp to force cache reload
                    ])
                );
            }
            // Handle failed deletion
            else {
                $this->errorMessage = implode(' ', $result['messages']);

                // Use toast helper for error
                toast()
                    ->error($this->errorMessage)
                    ->autoDismiss(10);

                Log::warning('Failed to delete calendar event', [
                    'request_id' => $this->requestId
                ]);

                $this->isProcessing = false;
            }
        } catch (\Exception $e) {
            // Set error message for UI display
            $this->errorMessage = 'An unexpected error occurred while deleting the calendar event. Please try again.';

            // Log the error with key context for debugging
            Log::error('Error in DeleteGoogleCalendarEvent component', [
                'request_id' => $this->requestId,
                'error' => $e->getMessage()
            ]);

            // Use toast helper for error
            toast()
                ->error($this->errorMessage)
                ->autoDismiss(10);

            $this->isProcessing = false;
        }
    }

    public function render()
    {
        $instructionRequest = InstructionRequests::with(['googleCalendarEvent', 'campus', 'librarian'])
            ->findOrFail($this->requestId);

        return view('livewire.delete-google-calendar-event', [
            'instructionRequest' => $instructionRequest
        ]);
    }
}
