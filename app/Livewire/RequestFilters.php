<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class RequestFilters extends Component
{
    // Change from private to public
    public bool $showingMyRequests = false;  // Initialize to false

    public function filterByLibrarian()
    {
        $this->showingMyRequests = true;  // Set to true when filtering

        $this->dispatch('filterByLibrarian', name: Auth::user()->display_name)
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
        $this->showingMyRequests = false;  // Set to false when clearing

        $this->dispatch('clearFilters')
            ->to('instruction-request-table');
    }

    public function render()
    {
        return view('livewire.request-filters');
    }
}
