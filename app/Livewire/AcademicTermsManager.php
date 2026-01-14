<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\TermService;
use App\Models\AcademicTerm;
use Carbon\Carbon;

class AcademicTermsManager extends Component
{
    public $selectedYear;
    public $years = [];
    public $showCreateModal = false;

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

    public function render()
    {
        $termService = app(TermService::class);
        $currentYear = $termService->getCurrentAcademicYear();

        return view('livewire.academic-terms-manager', [
            'currentYear' => $currentYear,
        ]);
    }
}
