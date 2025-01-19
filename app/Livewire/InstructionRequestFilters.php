<?php

namespace App\Livewire;

use App\Models\Campus;
use Livewire\Component;
use Illuminate\Support\Carbon;

class InstructionRequestFilters extends Component
{
    public $dateRange = '';
    public $selectedCampus = '';
    public $selectedStatus = '';
    public $selectedType = '';
    public $startDate = '';
    public $endDate = '';

    public function mount()
    {
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');
    }

    public function render()
    {
        $campuses = Campus::orderBy('name')->get();
        $statuses = [
            'received' => 'Received',
            'assigned' => 'Assigned',
            'accepted' => 'Accepted',
            'completed' => 'Completed'
        ];

        $types = [
            'on-campus' => 'On Campus',
            'remote' => 'Remote',
            'asynchronous' => 'Asynchronous'
        ];

        $dateRanges = [
            'today' => 'Today',
            'this_week' => 'This Week',
            'this_month' => 'This Month',
            'last_month' => 'Last Month',
            'custom' => 'Custom Range'
        ];

        return view('instruction_requests.partials.filters-content', compact(
            'campuses',
            'statuses',
            'types',
            'dateRanges'
        ));
    }

    public function updatedDateRange()
    {
        if ($this->dateRange !== 'custom') {
            $this->setDateRangeValues();
        }
        $this->emitFilterChanged();
    }

    protected function setDateRangeValues()
    {
        match($this->dateRange) {
            'today' => $this->setTodayRange(),
            'this_week' => $this->setWeekRange(),
            'this_month' => $this->setMonthRange(),
            'last_month' => $this->setLastMonthRange(),
            default => $this->clearDateRange()
        };
    }

    protected function setTodayRange()
    {
        $this->startDate = Carbon::today()->format('Y-m-d');
        $this->endDate = Carbon::today()->format('Y-m-d');
    }

    protected function setWeekRange()
    {
        $this->startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfWeek()->format('Y-m-d');
    }

    protected function setMonthRange()
    {
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    protected function setLastMonthRange()
    {
        $this->startDate = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d');
    }

    protected function clearDateRange()
    {
        $this->startDate = '';
        $this->endDate = '';
    }

    public function updated($field)
    {
        if ($field !== 'dateRange') {
            $this->emitFilterChanged();
        }
    }

    public function resetFilters()
    {
        $this->reset(['dateRange', 'selectedCampus', 'selectedStatus', 'selectedType', 'startDate', 'endDate']);
        $this->emitFilterChanged();
    }

    public function applyFilters()
    {
        $this->emitFilterChanged();
    }

    protected function emitFilterChanged()
    {
        $this->dispatch('filterChanged', [
            'dateRange' => $this->dateRange,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'campus' => $this->selectedCampus,
            'status' => $this->selectedStatus,
            'type' => $this->selectedType,
        ]);
    }
}
