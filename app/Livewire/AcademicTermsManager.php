<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\TermService;
use App\Models\AcademicTerm;
use Carbon\Carbon;
use Masmerise\Toaster\Toaster;

class AcademicTermsManager extends Component
{
    public $selectedYear;
    public $years = [];
    public $showCreateModal = false;
    public $newYear = '';

    public $fallStart;
    public $fallEnd;

    public $winterStart;
    public $winterEnd;

    public $springStart;
    public $springEnd;

    public $summerStart;
    public $summerEnd;

    public function mount()
    {
        $this->loadYears();
        $this->selectCurrentYear();
    }

    protected function loadYears()
    {
        $this->years = AcademicTerm::distinctYears()->toArray();
    }

    protected function selectCurrentYear()
    {
        $termService = app(TermService::class);
        $currentYear = $termService->getCurrentAcademicYear();

        if (in_array($currentYear, $this->years)) {
            $this->selectYear($currentYear);
        } elseif (!empty($this->years)) {
            $this->selectYear($this->years[0]);
        }
    }

    public function selectYear($year)
    {
        $this->selectedYear = $year;
        $this->loadTerms();
    }

    protected function loadTerms()
    {
        $terms = AcademicTerm::forYear($this->selectedYear);

        foreach ($terms as $term) {
            if ($term->term_name === 'fall') {
                $this->fallStart = $term->start_date->format('Y-m-d');
                $this->fallEnd = $term->end_date->format('Y-m-d');
            } elseif ($term->term_name === 'winter') {
                $this->winterStart = $term->start_date->format('Y-m-d');
                $this->winterEnd = $term->end_date->format('Y-m-d');
            } elseif ($term->term_name === 'spring') {
                $this->springStart = $term->start_date->format('Y-m-d');
                $this->springEnd = $term->end_date->format('Y-m-d');
            } elseif ($term->term_name === 'summer') {
                $this->summerStart = $term->start_date->format('Y-m-d');
                $this->summerEnd = $term->end_date->format('Y-m-d');
            }
        }
    }

    protected function calculateWeeks($start, $end)
    {
        if (!$start || !$end) return 0;

        $startDate = Carbon::parse($start);
        $endDate = Carbon::parse($end);

        return (int) $startDate->diffInWeeks($endDate);
    }

    public function getTotalWeeksProperty()
    {
        return $this->calculateWeeks($this->fallStart, $this->fallEnd) +
               $this->calculateWeeks($this->winterStart, $this->winterEnd) +
               $this->calculateWeeks($this->springStart, $this->springEnd) +
               $this->calculateWeeks($this->summerStart, $this->summerEnd);
    }

    public function cancel()
    {
        $this->loadTerms();
    }

    public function save()
    {
        $this->validate([
            'fallStart' => 'required|date',
            'fallEnd' => 'required|date|after:fallStart',
            'winterStart' => 'required|date',
            'winterEnd' => 'required|date|after:winterStart',
            'springStart' => 'required|date',
            'springEnd' => 'required|date|after:springStart',
            'summerStart' => 'required|date',
            'summerEnd' => 'required|date|after:summerStart',
        ]);

        AcademicTerm::updateOrCreate(
            ['academic_year' => $this->selectedYear, 'term_name' => 'fall'],
            ['start_date' => $this->fallStart, 'end_date' => $this->fallEnd, 'sort_order' => 1]
        );

        AcademicTerm::updateOrCreate(
            ['academic_year' => $this->selectedYear, 'term_name' => 'winter'],
            ['start_date' => $this->winterStart, 'end_date' => $this->winterEnd, 'sort_order' => 2]
        );

        AcademicTerm::updateOrCreate(
            ['academic_year' => $this->selectedYear, 'term_name' => 'spring'],
            ['start_date' => $this->springStart, 'end_date' => $this->springEnd, 'sort_order' => 3]
        );

        AcademicTerm::updateOrCreate(
            ['academic_year' => $this->selectedYear, 'term_name' => 'summer'],
            ['start_date' => $this->summerStart, 'end_date' => $this->summerEnd, 'sort_order' => 4]
        );

        app(Toaster::class)->success('Academic year saved successfully.');
        $this->loadTerms();
    }

    public function showCreateYearModal()
    {
        $this->showCreateModal = true;
        $this->newYear = '';
    }

    public function createYear()
    {
        $this->validate([
            'newYear' => 'required|regex:/^\d{4}-\d{4}$/',
        ]);

        if (AcademicTerm::where('academic_year', $this->newYear)->exists()) {
            app(Toaster::class)->error('This academic year already exists.');
            return;
        }

        $termService = app(TermService::class);
        [$startYear, $endYear] = explode('-', $this->newYear);

        $fallStart = Carbon::create((int)$startYear, 9, 22);
        $fallEnd = Carbon::create((int)$startYear, 12, 19);

        $winterStart = $termService->getFirstMondayAfterNewYear((int)$endYear);
        $winterEnd = Carbon::create((int)$endYear, 3, 20);

        $springStart = $termService->getNextMonday($winterEnd);
        $springEnd = Carbon::create((int)$endYear, 6, 12);

        $summerStart = $termService->getNextMonday($springEnd);
        $summerEnd = Carbon::create((int)$endYear, 8, 21);

        AcademicTerm::create(['academic_year' => $this->newYear, 'term_name' => 'fall', 'start_date' => $fallStart, 'end_date' => $fallEnd, 'sort_order' => 1]);
        AcademicTerm::create(['academic_year' => $this->newYear, 'term_name' => 'winter', 'start_date' => $winterStart, 'end_date' => $winterEnd, 'sort_order' => 2]);
        AcademicTerm::create(['academic_year' => $this->newYear, 'term_name' => 'spring', 'start_date' => $springStart, 'end_date' => $springEnd, 'sort_order' => 3]);
        AcademicTerm::create(['academic_year' => $this->newYear, 'term_name' => 'summer', 'start_date' => $summerStart, 'end_date' => $summerEnd, 'sort_order' => 4]);

        $this->loadYears();
        $this->selectYear($this->newYear);
        $this->showCreateModal = false;

        app(Toaster::class)->success('Academic year created successfully.');
    }

    public function deleteYear($year)
    {
        $termService = app(TermService::class);
        $currentYear = $termService->getCurrentAcademicYear();

        if ($year === $currentYear) {
            app(Toaster::class)->error('Cannot delete the current academic year.');
            return;
        }

        AcademicTerm::where('academic_year', $year)->delete();
        $this->loadYears();

        if ($this->selectedYear === $year) {
            $this->selectCurrentYear();
        }

        app(Toaster::class)->success('Academic year deleted successfully.');
    }

    public function render()
    {
        $termService = app(TermService::class);
        $currentYear = $termService->getCurrentAcademicYear();

        return view('livewire.academic-terms-manager', [
            'currentYear' => $currentYear,
        ]);
    }
}
