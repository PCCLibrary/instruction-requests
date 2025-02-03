<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class RequestFilters extends Component
{
    public function filterByLibrarian()
    {
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
        $this->dispatch('clearFilters')
            ->to('instruction-request-table');
    }

    public function render()
    {
        return view('livewire.request-filters');
    }
}
