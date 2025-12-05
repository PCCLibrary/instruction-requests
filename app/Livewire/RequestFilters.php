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
    public bool $showingUnassigned = false;  // New property
    public bool $showingNoPreference = false;  // New property
    public bool $showingExpiringUnassigned = false;  // New property
    public bool $showingReceivedDefault = true;  // Default filter on page load

    public function mount()
    {
        // Show the default "received" filter pill on page load
        $this->showingReceivedDefault = true;
    }

    public function filterByLibrarian()
    {
        // Reset all filter states
        $this->resetFilterStates();

        // Set active filter
        $this->showingMyRequests = true;

        $this->dispatch('filterByLibrarian', name: Auth::user()->display_name)
            ->to('instruction-request-table');
    }

    public function filterMyRequests()
    {
        $this->resetFilterStates();
        $this->showingMyRequests = true;
        $this->dispatch('filterMyRequests')->to('instruction-request-table');
    }

    public function filterUnassigned()
    {
        $this->resetFilterStates();
        $this->showingUnassigned = true;
        $this->dispatch('filterUnassigned')->to('instruction-request-table');
    }

    public function filterNoPreference()
    {
        $this->resetFilterStates();
        $this->showingNoPreference = true;
        $this->dispatch('filterNoPreference')->to('instruction-request-table');
    }

    public function filterExpiringUnassigned()
    {
        $this->resetFilterStates();
        $this->showingExpiringUnassigned = true;
        $this->dispatch('filterExpiringUnassigned')->to('instruction-request-table');
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
        $this->showingUnassigned = false;
        $this->showingNoPreference = false;
        $this->showingExpiringUnassigned = false;
        $this->showingReceivedDefault = false;  // Clear default filter pill when other filters applied
    }

    public function render()
    {
        return view('livewire.request-filters');
    }
}
