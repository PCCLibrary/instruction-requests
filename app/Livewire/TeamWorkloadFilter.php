<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Campus;

class TeamWorkloadFilter extends Component
{
    public $campus = null;

    public function updatedCampus()
    {
        $this->dispatch('campusFilterUpdated', campus: $this->campus);
    }

    public function clearFilters()
    {
        $this->campus = null;
        $this->dispatch('campusFilterUpdated', campus: null);
    }

    public function removeFilter($filterName)
    {
        $this->$filterName = null;
        $this->dispatch('campusFilterUpdated', campus: null);
    }

    public function render()
    {
        $campuses = Campus::forLibrarians()->get();

        return view('livewire.team-workload-filter', [
            'campuses' => $campuses
        ]);
    }
}
