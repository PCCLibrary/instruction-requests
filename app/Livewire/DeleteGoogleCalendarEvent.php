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

    public function mount(int $requestId)
    {
        $this->requestId = $requestId;
    }

    /**
     * Delete the Google Calendar event associated with an instruction request
     */
    public function deleteEvent()
    {
        try {
            // Find the instruction request and ensure it has a calendar event
            $instructionRequest = InstructionRequests::with('googleCalendarEvent')
                ->findOrFail($this->requestId);

            if (!$instructionRequest->googleCalendarEvent) {
                $this->dispatch('toast',
                    type: 'error',
                    message: 'No calendar event found for this instruction request.'
                );
                return;
            }

            // Use the calendar service to delete the event
            $calendarService = app(CalendarService::class);
            $result = $calendarService->deleteEvent($instructionRequest->googleCalendarEvent);

            // Handle successful deletion
            if ($result['success']) {
                $this->dispatch('toast',
                    type: 'success',
                    message: implode(' ', $result['messages'])
                );
                
                // Redirect back to the instruction request edit page
                $this->redirect(route('instructionRequests.edit', $result['instruction_request_id']));
            } 
            // Handle failed deletion
            else {
                $this->dispatch('toast',
                    type: 'error',
                    message: implode(' ', $result['messages'])
                );
                
                // Stay on the current page, but allow the user to try again
                Log::warning('Failed to delete calendar event', [
                    'request_id' => $this->requestId,
                    'messages' => $result['messages']
                ]);
            }
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error in DeleteGoogleCalendarEvent component', [
                'request_id' => $this->requestId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Provide a user-friendly error message
            $this->dispatch('toast',
                type: 'error',
                message: 'An unexpected error occurred while deleting the calendar event. Please try again.'
            );
        }
    }

    public function render()
    {
        $instructionRequest = InstructionRequests::with('googleCalendarEvent')
            ->findOrFail($this->requestId);
            
        return view('livewire.delete-google-calendar-event', [
            'instructionRequest' => $instructionRequest
        ]);
    }
}
