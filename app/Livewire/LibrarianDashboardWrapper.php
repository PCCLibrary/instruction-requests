<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Log;

class LibrarianDashboardWrapper extends Component
{
    public bool $scheduleModalOpen = false;
    public ?int $scheduleRequestId = null;

    protected $listeners = [
        'open-schedule-modal' => 'handleOpenModal',
        'close-modal' => 'closeModal',
        'googleCalendarEventCreated' => 'handleCalendarEventCreated'
    ];

    public function handleOpenModal($id)
    {
        Log::info('Dashboard wrapper received event', [
            'id' => $id
        ]);

        $this->scheduleModalOpen = true;
        $this->scheduleRequestId = $id;

        Log::info('Dashboard wrapper state set', [
            'scheduleModalOpen' => $this->scheduleModalOpen,
            'scheduleRequestId' => $this->scheduleRequestId
        ]);
    }

    public function closeModal()
    {
        Log::info('Dashboard wrapper closing modal');
        $this->scheduleModalOpen = false;
        $this->scheduleRequestId = null;
    }

    public function handleCalendarEventCreated()
    {
        Log::info('Dashboard wrapper: Calendar event created, closing modal and refreshing table');

        // Close the modal
        $this->closeModal();

        // Dispatch table refresh event to MyActiveRequestsTable
        $this->dispatch('pg:eventRefresh-MyActiveRequestsTable');
    }

    public function render()
    {
        return view('livewire.librarian-dashboard-wrapper');
    }
}
