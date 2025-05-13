<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class RequestFilters extends Component
{
    // State tracking properties
    public bool $showingMyRequests = false;  // Initialize to false
    public bool $showingExpiringReceived = false;  // New property
    public bool $showingRejected = false;  // New property

    public function filterByLibrarian()
    {
        // Reset all filter states
        $this->resetFilterStates();

        // Set active filter
        $this->showingMyRequests = true;

        $this->dispatch('filterByLibrarian', name: Auth::user()->display_name)
            ->to('instruction-request-table');
    }

    // New method for Expiring Received filter
    public function filterExpiringReceived()
    {
        // Reset all filter states
        $this->resetFilterStates();

        // Set active filter
        $this->showingExpiringReceived = true;

        $this->dispatch('filterExpiringReceived')
            ->to('instruction-request-table');
    }

    // New method for Rejected filter
    public function filterRejected()
    {
        // Reset all filter states
        $this->resetFilterStates();

        // Set active filter
        $this->showingRejected = true;

        $this->dispatch('filterRejected')
            ->to('instruction-request-table');
    }

    public function filterByCampus($code)
    {
        $this->dispatch('filterByCampus', code: $code)
            ->to('instruction-request-table');
    }

    public function filterByStatus($status)
    {
        $this->dispatch('filterByStatus', status: $status)
            ->to('instruction-request-table');
    }

    public function clearFilters()
    {
        // Reset all filter states
        $this->resetFilterStates();

        $this->dispatch('clearFilters')
            ->to('instruction-request-table');
    }

    // Helper method to reset all filter states
    private function resetFilterStates()
    {
        $this->showingMyRequests = false;
        $this->showingExpiringReceived = false;
        $this->showingRejected = false;
    }

    public function render()
    {
        return view('livewire.request-filters');
    }
}
