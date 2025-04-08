<?php

namespace App\Livewire;

use App\Models\InstructionRequests;
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

    public function deleteEvent(CalendarService $calendarService)
    {
        $instructionRequest = InstructionRequests::with('googleCalendarEvent')->findOrFail($this->requestId);

        try {
            $result = $calendarService->deleteEvent($instructionRequest);

            if ($result['success']) {
                session()->flash('success', $result['message']);
                Log::info($result['message'], ['request_id' => $this->requestId]);
            } else {
                session()->flash('error', $result['message']);
                Log::error($result['message'], ['request_id' => $this->requestId, 'error_code' => $result['code'] ?? '']);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while deleting the calendar event: ' . $e->getMessage());
            Log::error('Error deleting calendar event', [
                'request_id' => $this->requestId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        } finally {
            // Update the request status back to "accepted"
            $instructionRequest->status = 'accepted';
            $instructionRequest->save();
        }

        // Redirect back to the edit page
        return redirect()->route('instructionRequests.edit', $this->requestId);
    }

    public function render()
    {
        $instructionRequest = InstructionRequests::findOrFail($this->requestId);
        return view('livewire.delete-google-calendar-event', ['instructionRequest' => $instructionRequest]);
    }
}
