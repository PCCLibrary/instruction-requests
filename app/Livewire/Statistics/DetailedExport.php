<?php

namespace App\Livewire\Statistics;

use Livewire\Component;
use App\Models\InstructionRequests;
use App\Models\Instructor;
use App\Models\AcademicTerm;
use App\Services\DepartmentService;
use App\Services\StatisticsService;
use App\Services\TermService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class DetailedExport extends Component
{
    public $activeTab = 'institutional';
    public $viewMode = 'year';
    public $startPeriod;
    public $endPeriod;
    public $campus = null;
    public $department = null;
    public $class = null;
    public $instructor = null;
    public $assignedLibrarian = null;
    public $departmentSearch = '';
    public $classSearch = '';
    public $instructorSearch = '';
    public $filtersExpanded = true;

    public $showColumnModal = false;
    public $columnVisibility = [];

    protected $departmentService;
    protected $statisticsService;
    protected $termService;

    public function boot(DepartmentService $departmentService, StatisticsService $statisticsService, TermService $termService)
    {
        $this->departmentService = $departmentService;
        $this->statisticsService = $statisticsService;
        $this->termService = $termService;
    }

    public function mount()
    {
        $availableYears = $this->statisticsService->getAvailableAcademicYears();
        $this->startPeriod = array_key_first($availableYears);
        $this->endPeriod = array_key_last($availableYears);

        $this->initializeColumnVisibility();

        // Dispatch initial filters to table on load
        $this->dispatchFiltersToTable();
    }

    public function updatedViewMode($value)
    {
        if ($value === 'year' || $value === 'fiscal_year') {
            $availableYears = $this->statisticsService->getAvailableAcademicYears();
            $this->startPeriod = array_key_first($availableYears);
            $this->endPeriod = array_key_last($availableYears);
        } elseif ($value === 'term') {
            $terms = $this->availableTerms;
            if ($terms->isNotEmpty()) {
                $this->startPeriod = $terms->first()->id;
                $this->endPeriod = $terms->last()->id;
            }
        } elseif ($value === 'month') {
            $this->startPeriod = now()->format('Y-m');
            $this->endPeriod = now()->format('Y-m');
        } elseif ($value === 'day') {
            $this->startPeriod = now()->subDays(30)->format('Y-m-d');
            $this->endPeriod = now()->format('Y-m-d');
        }
    }

    public function getCurrentAcademicYear(): int
    {
        return $this->statisticsService->getCurrentAcademicYear();
    }

    public function getAvailableAcademicYears(): array
    {
        return $this->statisticsService->getAvailableAcademicYears();
    }

    public function getAvailableTermsProperty()
    {
        return $this->termService->getAllTermsOrdered();
    }

    public function getCampuses()
    {
        return $this->statisticsService->getCampuses();
    }

    public function getDepartments()
    {
        return $this->statisticsService->getDepartments();
    }

    public function getInstructors()
    {
        return $this->statisticsService->getInstructors();
    }

    public function getLibrarians()
    {
        return $this->statisticsService->getLibrarians();
    }

    public function getFilteredInstructorsProperty()
    {
        $query = Instructor::query();

        if (!empty($this->instructorSearch)) {
            $search = strtolower($this->instructorSearch);

            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(display_name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"]);
            });
        }

        return $query->orderBy('display_name')
                     ->limit(100)
                     ->get();
    }

    public function getFilteredDepartmentsProperty()
    {
        $allDepartments = $this->departmentService->getAllDepartments();

        if (empty($this->departmentSearch)) {
            return $allDepartments;
        }

        $search = strtolower($this->departmentSearch);
        $filtered = [];

        foreach ($allDepartments as $code => $name) {
            if (str_contains(strtolower($name), $search) || str_contains(strtolower($code), $search)) {
                $filtered[$code] = $name;
            }
        }

        return $filtered;
    }

    public function getFilteredClassesProperty()
    {
        $query = InstructionRequests::select(
            DB::raw("DISTINCT CONCAT(UPPER(department), '-', course_number) as class_code"),
            'department',
            'course_number'
        )
        ->whereNotNull('department')
        ->whereNotNull('course_number')
        ->where('department', '!=', '')
        ->where('course_number', '!=', '')
        ->orderBy('department')
        ->orderBy('course_number');

        $classes = $query->get()->map(function($item) {
            return [
                'code' => $item->class_code,
                'display' => str_replace('-', ' ', $item->class_code)
            ];
        });

        if (empty($this->classSearch)) {
            return $classes->take(100);
        }

        $search = strtolower($this->classSearch);
        return $classes->filter(function($class) use ($search) {
            return str_contains(strtolower($class['display']), $search) ||
                   str_contains(strtolower($class['code']), $search);
        })->take(100);
    }

    public function updatedInstructorSearch()
    {
        $filteredInstructors = $this->filteredInstructors;
        $this->dispatch('instructors-updated', $filteredInstructors->toArray());
    }

    public function clearFilters()
    {
        $currentAcademicYear = $this->getCurrentAcademicYear();
        $this->activeTab = 'institutional';
        $this->viewMode = 'year';
        $this->startPeriod = $currentAcademicYear;
        $this->endPeriod = $currentAcademicYear;
        $this->campus = null;
        $this->department = null;
        $this->class = null;
        $this->instructor = null;
        $this->assignedLibrarian = null;
        $this->departmentSearch = '';
        $this->classSearch = '';

        $this->dispatch('clear-instructor');
        $this->dispatchFiltersToTable();
    }

    public function removeFilter($filterName)
    {
        $this->$filterName = null;

        if ($filterName === 'instructor') {
            $this->dispatch('clear-instructor');
        }

        $this->dispatchFiltersToTable();
    }

    /**
     * Dispatch current filter state to PowerGrid table
     */
    private function dispatchFiltersToTable()
    {
        // Convert periods to date range based on view mode
        [$startDate, $endDate] = $this->convertPeriodsToDateRange();

        $this->dispatch('applyFilters', filters: [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'campus' => $this->campus,
            'department' => $this->department,
            'class' => $this->class,
            'instructor' => $this->instructor,
            'assignedLibrarian' => $this->assignedLibrarian,
        ])->to('statistics.detailed-export-table');
    }

    /**
     * Convert period selections to date range
     */
    private function convertPeriodsToDateRange(): array
    {
        switch ($this->viewMode) {
            case 'year':
                $endYear = (int) $this->endPeriod;
                return [
                    "{$this->startPeriod}-09-01",
                    ($endYear + 1) . "-08-31"
                ];

            case 'fiscal_year':
                $endYear = (int) $this->endPeriod;
                return [
                    "{$this->startPeriod}-07-01",
                    ($endYear + 1) . "-06-30"
                ];

            case 'term':
                $startTerm = AcademicTerm::find($this->startPeriod);
                $endTerm = AcademicTerm::find($this->endPeriod);

                // Handle case where terms aren't found (e.g., switching from year mode)
                if (!$startTerm || !$endTerm) {
                    // Fallback to current academic year
                    $currentYear = $this->getCurrentAcademicYear();
                    return [
                        "{$currentYear}-09-01",
                        ($currentYear + 1) . "-08-31"
                    ];
                }

                return [
                    $startTerm->start_date->format('Y-m-d'),
                    $endTerm->end_date->format('Y-m-d')
                ];

            case 'month':
                return [
                    $this->startPeriod . '-01',
                    Carbon::parse($this->endPeriod)->endOfMonth()->format('Y-m-d')
                ];

            case 'day':
                return [$this->startPeriod, $this->endPeriod];

            default:
                return [now()->subDays(30)->format('Y-m-d'), now()->format('Y-m-d')];
        }
    }

    /**
     * Watch for filter changes and dispatch to table
     */
    public function updated($propertyName)
    {
        // Dispatch filters when any filter property changes
        if (in_array($propertyName, [
            'startPeriod', 'endPeriod', 'viewMode',
            'campus', 'department', 'class', 'instructor', 'assignedLibrarian'
        ])) {
            $this->dispatchFiltersToTable();
        }
    }

    public function toggleColumnModal()
    {
        $this->showColumnModal = !$this->showColumnModal;
    }

    public function selectAllColumns()
    {
        foreach ($this->columnVisibility as $group => &$columns) {
            foreach ($columns as &$column) {
                $column['visible'] = true;
            }
        }
    }

    public function deselectAllColumns()
    {
        foreach ($this->columnVisibility as $group => &$columns) {
            foreach ($columns as &$column) {
                $column['visible'] = false;
            }
        }
    }

    private function initializeColumnVisibility()
    {
        $this->columnVisibility = [
            'request_information' => [
                ['id' => 'id', 'label' => 'ID', 'visible' => true],
                ['id' => 'status', 'label' => 'Status', 'visible' => true],
                ['id' => 'instruction_type', 'label' => 'Instruction Type', 'visible' => true],
            ],
        ];
    }

    public function render()
    {
        return view('livewire.statistics.detailed-export', [
            'availableAcademicYears' => $this->getAvailableAcademicYears(),
            'availableTerms' => $this->availableTerms,
            'campuses' => $this->getCampuses(),
            'departments' => $this->getDepartments(),
            'instructors' => $this->getInstructors(),
            'librarians' => $this->getLibrarians(),
        ]);
    }
}
