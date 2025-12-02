<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Campus;

class SchedulerRequestFilters extends Component
{
    // State tracking properties for quick filters
    public bool $showingUnassigned = false;
    public bool $showingNoPreference = false;
    public bool $showingExpiringUnassigned = false;
    public bool $showingRejected = false;

    public function filterUnassigned()
    {
        // Reset all filter states
        $this->resetFilterStates();

        // Set active filter
        $this->showingUnassigned = true;

        $this->dispatch('filterUnassigned')
            ->to('scheduler-request-table');
    }

    public function filterNoPreference()
    {
        // Reset all filter states
        $this->resetFilterStates();

        // Set active filter
        $this->showingNoPreference = true;

        $this->dispatch('filterNoPreference')
            ->to('scheduler-request-table');
    }

    public function filterExpiringUnassigned()
    {
        // Reset all filter states
        $this->resetFilterStates();

        // Set active filter
        $this->showingExpiringUnassigned = true;

        $this->dispatch('filterExpiringUnassigned')
            ->to('scheduler-request-table');
    }

    public function filterRejected()
    {
        // Reset all filter states
        $this->resetFilterStates();

        // Set active filter
        $this->showingRejected = true;

        $this->dispatch('filterRejected')
            ->to('scheduler-request-table');
    }

    public function clearFilters()
    {
        // Reset all filter states
        $this->resetFilterStates();

        $this->dispatch('clearFilters')
            ->to('scheduler-request-table');
    }

    // Helper method to reset all filter states
    private function resetFilterStates()
    {
        $this->showingUnassigned = false;
        $this->showingNoPreference = false;
        $this->showingExpiringUnassigned = false;
        $this->showingRejected = false;
    }

    public function render()
    {
        return view('livewire.scheduler-request-filters');
    }
}
