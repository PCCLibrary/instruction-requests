<?php

namespace App\Livewire;

use App\Models\InstructionRequests;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DashboardCalendar extends Component
{
    // Component props
    public ?int $userId = null;

    // State
    public int $year;
    public int $month;
    public ?int $selectedDay = null;
    public ?int $selectedLibrarianId = null;

    // Filters
    public bool $showOnCampus = true;
    public bool $showRemote = true;
    public bool $showAsynchronous = true;

    public function mount(?int $userId = null)
    {
        $this->userId = $userId;

        // Default to current month
        $this->year = now()->year;
        $this->month = now()->month;

        // Default to today
        $this->selectedDay = now()->day;

        // Always default to the provided userId (for librarian view, this is their own ID)
        if ($this->userId) {
            $this->selectedLibrarianId = $this->userId;
        }
    }

    public function previousMonth()
    {
        $date = Carbon::create($this->year, $this->month, 1)->subMonth();
        $this->year = $date->year;
        $this->month = $date->month;
        $this->selectedDay = null; // Clear selection when changing months
    }

    public function nextMonth()
    {
        $date = Carbon::create($this->year, $this->month, 1)->addMonth();
        $this->year = $date->year;
        $this->month = $date->month;
        $this->selectedDay = null; // Clear selection when changing months
    }

    public function goToCurrentMonth()
    {
        $this->year = now()->year;
        $this->month = now()->month;
        $this->selectedDay = now()->day;
    }

    public function selectDay(int $day)
    {
        $this->selectedDay = $day;
    }

    public function updatedSelectedLibrarianId()
    {
        // Refresh when librarian changes
        $this->selectedDay = null;
    }

    public function getEventsForMonthProperty(): Collection
    {
        $startOfMonth = Carbon::create($this->year, $this->month, 1)->startOfDay();
        $endOfMonth = $startOfMonth->copy()->endOfMonth()->endOfDay();

        $query = InstructionRequests::query()
            ->leftJoin('instruction_request_details', 'instruction_requests.id', '=', 'instruction_request_details.instruction_requests_id')
            ->leftJoin('instructors', 'instruction_requests.instructor_id', '=', 'instructors.id')
            ->leftJoin('classes', 'instruction_requests.class_id', '=', 'classes.id')
            ->leftJoin('users as librarians', 'instruction_request_details.assigned_librarian_id', '=', 'librarians.id')
            ->whereBetween('instruction_request_details.instruction_datetime', [$startOfMonth, $endOfMonth])
            ->where('instruction_requests.status', '!=', 'completed') // Exclude completed requests
            ->select([
                'instruction_requests.id',
                'instruction_requests.instruction_type',
                'instruction_requests.status',
                'instruction_request_details.instruction_datetime',
                'instructors.display_name as instructor_name',
                'classes.course_name',
                'librarians.display_name as librarian_name',
            ]);

        // Filter by librarian if specified
        if ($this->selectedLibrarianId) {
            $query->where('instruction_request_details.assigned_librarian_id', $this->selectedLibrarianId);
        }

        // Filter by instruction type based on checkboxes
        $types = [];
        if ($this->showOnCampus) $types[] = 'on-campus';
        if ($this->showRemote) $types[] = 'remote';
        if ($this->showAsynchronous) $types[] = 'asynchronous';

        if (!empty($types)) {
            $query->whereIn('instruction_requests.instruction_type', $types);
        } else {
            // If no types selected, return empty
            return collect();
        }

        return $query->get();
    }

    public function getEventCountsProperty(): array
    {
        $counts = [];

        foreach ($this->eventsForMonth as $event) {
            $day = Carbon::parse($event->instruction_datetime)->day;

            if (!isset($counts[$day])) {
                $counts[$day] = [
                    'on-campus' => 0,
                    'remote' => 0,
                    'asynchronous' => 0,
                ];
            }

            $counts[$day][$event->instruction_type]++;
        }

        return $counts;
    }

    public function getSelectedDayEventsProperty(): Collection
    {
        if (!$this->selectedDay) {
            return collect();
        }

        $selectedDate = Carbon::create($this->year, $this->month, $this->selectedDay);

        return $this->eventsForMonth->filter(function ($event) use ($selectedDate) {
            return Carbon::parse($event->instruction_datetime)->isSameDay($selectedDate);
        })->sortBy('instruction_datetime');
    }

    public function getCalendarGridProperty(): array
    {
        $firstDayOfMonth = Carbon::create($this->year, $this->month, 1);
        $daysInMonth = $firstDayOfMonth->daysInMonth;
        $startDayOfWeek = $firstDayOfMonth->dayOfWeekIso; // 1 = Monday, 7 = Sunday

        $grid = [];
        $currentDay = 1;

        // Calculate number of weeks needed
        $totalCells = $startDayOfWeek - 1 + $daysInMonth;
        $weeks = ceil($totalCells / 7);

        for ($week = 0; $week < $weeks; $week++) {
            $grid[$week] = [];

            for ($dayOfWeek = 1; $dayOfWeek <= 7; $dayOfWeek++) {
                $cellIndex = $week * 7 + $dayOfWeek;

                if ($cellIndex < $startDayOfWeek || $currentDay > $daysInMonth) {
                    $grid[$week][] = null; // Empty cell
                } else {
                    $grid[$week][] = $currentDay;
                    $currentDay++;
                }
            }
        }

        return $grid;
    }

    public function getLibrariansProperty(): Collection
    {
        return User::orderedLibrariansScope()->get();
    }

    public function render()
    {
        $today = now();
        $isCurrentMonth = $this->year === $today->year && $this->month === $today->month;

        return view('livewire.dashboard-calendar', [
            'calendarGrid' => $this->calendarGrid,
            'eventCounts' => $this->eventCounts,
            'selectedDayEvents' => $this->selectedDayEvents,
            'librarians' => $this->librarians, // Always load librarians
            'isCurrentMonth' => $isCurrentMonth,
            'todayDay' => $isCurrentMonth ? $today->day : null,
        ]);
    }
}
