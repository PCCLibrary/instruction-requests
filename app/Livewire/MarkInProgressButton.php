<?php

namespace App\Livewire;

use App\Services\InstructionRequestService;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class MarkInProgressButton extends Component
{
    public $requestId;

    public function mount($requestId)
    {
        $this->requestId = $requestId;
    }

    public function markInProgress()
    {
        try {
            $instructionRequestService = app(InstructionRequestService::class);
            $instructionRequestService->updateInstructionRequest([
                'status' => 'in_progress'
            ], $this->requestId);

            app(Toaster::class)->success('Request marked as in progress.');

            return redirect()->route('instructionRequests.edit', $this->requestId);
        } catch (\Exception $e) {
            Log::error('Failed to mark request as in progress', [
                'request_id' => $this->requestId,
                'error' => $e->getMessage()
            ]);
            app(Toaster::class)->error('Failed to update request status.');
        }
    }

    public function render()
    {
        return view('livewire.mark-in-progress-button');
    }
}
