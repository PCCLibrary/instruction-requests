<?php

namespace App\Livewire;

use App\Models\InstructionRequests;
use App\Models\GoogleCalendarEvent;
use App\Services\CalendarService;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

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
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => $this->errorMessage
                ]);
                $this->isProcessing = false;
                return;
            }

            // Use the calendar service to delete the event
            $calendarService = app(CalendarService::class);
            $result = $calendarService->deleteEvent($instructionRequest->googleCalendarEvent);

            // Handle successful deletion
            if ($result['success']) {
                $message = implode(' ', $result['messages']);

                // Use toast notification for success
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => $message,
                    'duration' => 5000 // 5 seconds
                ]);

                // Make sure we're returning the redirect
                return $this->redirect(
                    route('instructionRequests.edit', [
                        'id' => $result['instruction_request_id'],
                        'calendar_deleted' => 'true'
                    ])
                );
            }
            // Handle failed deletion
            else {
                $this->errorMessage = implode(' ', $result['messages']);

                // Use toast notification for error
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => $this->errorMessage,
                    'duration' => 10000 // 10 seconds for errors
                ]);

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

            // Dispatch a toast notification
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => $this->errorMessage,
                'duration' => 10000 // 10 seconds for errors
            ]);

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
