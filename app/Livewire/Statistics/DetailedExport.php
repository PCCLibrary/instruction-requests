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
    public $tempColumnVisibility = []; // Staging array for modal changes
    public $columnVisibilityKey; // Unique key to force PowerGrid re-mount

    protected $departmentService;
    protected $statisticsService;
    protected $termService;

    public function boot(DepartmentService $departmentService, StatisticsService $statisticsService, TermService $termService)
    {
        $this->departmentService = $departmentService;
        $this->statisticsService = $statisticsService;
        $this->termService = $termService;
    }

    /**
     * Computed property to extract visible column IDs
     * This will be passed to the PowerGrid child component
     */
    public function getVisibleColumnsProperty()
    {
        $visibleIds = [];
        foreach ($this->columnVisibility as $group => $columns) {
            foreach ($columns as $column) {
                if ($column['visible']) {
                    $visibleIds[] = $column['id'];
                }
            }
        }
        return $visibleIds;
    }

    public function mount()
    {
        $availableYears = $this->statisticsService->getAvailableAcademicYears();
        $this->startPeriod = array_key_first($availableYears);
        $this->endPeriod = array_key_last($availableYears);

        // Initialize column visibility from session or defaults
        $this->initializeColumnVisibility();

        // Load persisted column visibility from session (user-specific)
        $sessionKey = 'detailed_export_columns_' . auth()->id();
        $savedVisibility = session($sessionKey);

        if ($savedVisibility) {
            // Merge saved state with current structure (in case new columns added)
            foreach ($this->columnVisibility as $group => &$columns) {
                if (isset($savedVisibility[$group])) {
                    foreach ($columns as $index => &$column) {
                        $savedColumn = collect($savedVisibility[$group])->firstWhere('id', $column['id']);
                        if ($savedColumn) {
                            $column['visible'] = $savedColumn['visible'];
                        }
                    }
                }
            }
        }

        // Initialize temp with actual column visibility state
        $this->tempColumnVisibility = $this->columnVisibility;

        // Initialize unique key for PowerGrid component
        $this->columnVisibilityKey = uniqid();

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
        if (!$this->showColumnModal) {
            // Opening modal - sync temp with current state
            $this->tempColumnVisibility = $this->columnVisibility;
        }
        $this->showColumnModal = !$this->showColumnModal;
    }

    public function applyColumnVisibility()
    {
        // Copy temp to actual
        $this->columnVisibility = $this->tempColumnVisibility;

        // Persist to session (user-specific)
        $sessionKey = 'detailed_export_columns_' . auth()->id();
        session([$sessionKey => $this->columnVisibility]);

        // CRITICAL: Change key to force PowerGrid component re-mount
        // This ensures columns() method is called again with new visibility state
        $this->columnVisibilityKey = uniqid();

        // Close modal
        $this->showColumnModal = false;
    }

    public function selectAllColumns()
    {
        foreach ($this->tempColumnVisibility as $group => &$columns) {
            foreach ($columns as &$column) {
                $column['visible'] = true;
            }
        }
    }

    public function deselectAllColumns()
    {
        foreach ($this->tempColumnVisibility as $group => &$columns) {
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
                ['id' => 'number_of_students', 'label' => 'Number of Students', 'visible' => true],
                ['id' => 'created_at_formatted', 'label' => 'Created Date', 'visible' => true],
            ],
            'course_information' => [
                ['id' => 'campus_name', 'label' => 'Campus', 'visible' => true],
                ['id' => 'department', 'label' => 'Department', 'visible' => true],
                ['id' => 'course_number', 'label' => 'Course Number', 'visible' => true],
                ['id' => 'course_crn', 'label' => 'Course CRN', 'visible' => true],
            ],
            'instructor_information' => [
                ['id' => 'instructor_name', 'label' => 'Instructor Name', 'visible' => true],
                ['id' => 'instructor_email', 'label' => 'Instructor Email', 'visible' => true],
            ],
            'librarian_information' => [
                ['id' => 'requested_librarian_name', 'label' => 'Requested Librarian', 'visible' => true],
                ['id' => 'librarian_name', 'label' => 'Assigned Librarian', 'visible' => true],
            ],
            'scheduling' => [
                ['id' => 'preferred_datetime', 'label' => 'Preferred Date & Time', 'visible' => true],
                ['id' => 'alternate_datetime', 'label' => 'Alternate Date & Time', 'visible' => true],
                ['id' => 'duration', 'label' => 'Duration', 'visible' => true],
                ['id' => 'asynchronous_instruction_ready_date', 'label' => 'Async Ready Date', 'visible' => true],
                ['id' => 'instruction_datetime_formatted', 'label' => 'Instruction Date/Time', 'visible' => true],
                ['id' => 'instruction_duration', 'label' => 'Instruction Duration', 'visible' => true],
                ['id' => 'room', 'label' => 'Room', 'visible' => true],
            ],
            'instruction_goals' => [
                ['id' => 'genai_discussion_interest', 'label' => 'GenAI Discussion Interest', 'visible' => true],
            ],
            'ada_provisions' => [
                ['id' => 'ada_provisions_needed', 'label' => 'ADA Provisions Needed', 'visible' => true],
                ['id' => 'ada_provisions_description', 'label' => 'ADA Accommodations Description', 'visible' => true],
            ],
            'materials_created' => [
                ['id' => 'video', 'label' => 'Video', 'visible' => true],
                ['id' => 'non_video', 'label' => 'Non-Video', 'visible' => true],
                ['id' => 'modified_tutorial', 'label' => 'Modified Tutorial', 'visible' => true],
                ['id' => 'embedded', 'label' => 'Embedded', 'visible' => true],
                ['id' => 'research_guide', 'label' => 'Research Guide', 'visible' => true],
                ['id' => 'handout', 'label' => 'Handout', 'visible' => true],
                ['id' => 'developed_assignment', 'label' => 'Developed Assignment', 'visible' => true],
                ['id' => 'other_materials', 'label' => 'Other Materials', 'visible' => true],
                ['id' => 'other_describe', 'label' => 'Other Materials Description', 'visible' => true],
            ],
        ];
    }

    /**
     * Get count of selected columns
     */
    public function getSelectedColumnCountProperty()
    {
        $count = 0;
        foreach ($this->columnVisibility as $group => $columns) {
            foreach ($columns as $column) {
                if ($column['visible']) {
                    $count++;
                }
            }
        }
        return $count;
    }

    /**
     * Calculate summary metrics for the filtered dataset
     */
    protected function calculateSummaryMetrics(): array
    {
        // Convert periods to date range based on view mode
        [$startDate, $endDate] = $this->convertPeriodsToDateRange();

        // Build base query with same filters as the table
        $query = InstructionRequests::query()
            ->leftJoin('instructors', 'instruction_requests.instructor_id', '=', 'instructors.id')
            ->leftJoin('instruction_request_details', 'instruction_requests.id', '=', 'instruction_request_details.instruction_requests_id');

        // Apply date range filter
        if ($startDate && $endDate) {
            $query->whereBetween('instruction_requests.created_at', [$startDate, $endDate]);
        }

        // Apply secondary filters (same as table)
        if ($this->campus) {
            $query->where('instruction_requests.campus_id', $this->campus);
        }

        if ($this->department) {
            $query->where('instruction_requests.department', $this->department);
        }

        if ($this->class) {
            $parts = explode('-', $this->class);
            if (count($parts) === 2) {
                $query->where('instruction_requests.department', $parts[0])
                      ->where('instruction_requests.course_number', $parts[1]);
            }
        }

        if ($this->instructor) {
            $query->where('instruction_requests.instructor_id', $this->instructor);
        }

        if ($this->assignedLibrarian) {
            $query->where('instruction_request_details.assigned_librarian_id', $this->assignedLibrarian);
        }

        // Calculate metrics
        $totalSessions = $query->count('instruction_requests.id');

        $totalStudents = $query->sum('instruction_requests.number_of_students') ?? 0;

        $uniqueClasses = $query->select(DB::raw('DISTINCT CONCAT(instruction_requests.department, "-", instruction_requests.course_number)'))
            ->whereNotNull('instruction_requests.department')
            ->whereNotNull('instruction_requests.course_number')
            ->count();

        $completedSessions = (clone $query)->where('instruction_requests.status', 'completed')->count();
        $completedPercentage = $totalSessions > 0 ? ($completedSessions / $totalSessions) * 100 : 0;

        $adaSessions = (clone $query)->where('instruction_requests.ada_provisions_needed', true)->count();
        $adaPercentage = $totalSessions > 0 ? ($adaSessions / $totalSessions) * 100 : 0;

        $genaiSessions = (clone $query)->whereNotNull('instruction_requests.genai_discussion_interest')
            ->where('instruction_requests.genai_discussion_interest', '!=', '')
            ->count();
        $genaiPercentage = $totalSessions > 0 ? ($genaiSessions / $totalSessions) * 100 : 0;

        $avgClassSize = $query->whereNotNull('instruction_requests.number_of_students')
            ->where('instruction_requests.number_of_students', '>', 0)
            ->avg('instruction_requests.number_of_students') ?? 0;

        $avgDuration = (clone $query)->whereNotNull('instruction_request_details.instruction_duration')
            ->where('instruction_request_details.instruction_duration', '>', 0)
            ->avg('instruction_request_details.instruction_duration') ?? 0;

        return [
            'totalSessions' => $totalSessions,
            'totalStudents' => (int) $totalStudents,
            'uniqueClasses' => $uniqueClasses,
            'completedSessions' => $completedSessions,
            'completedPercentage' => $completedPercentage,
            'adaSessions' => $adaSessions,
            'adaPercentage' => $adaPercentage,
            'genaiSessions' => $genaiSessions,
            'genaiPercentage' => $genaiPercentage,
            'avgClassSize' => $avgClassSize,
            'avgDuration' => $avgDuration,
        ];
    }

    public function render()
    {
        $summaryMetrics = $this->calculateSummaryMetrics();

        return view('livewire.statistics.detailed-export', [
            'availableAcademicYears' => $this->getAvailableAcademicYears(),
            'availableTerms' => $this->availableTerms,
            'campuses' => $this->getCampuses(),
            'departments' => $this->getDepartments(),
            'instructors' => $this->getInstructors(),
            'librarians' => $this->getLibrarians(),
            'totalSessions' => $summaryMetrics['totalSessions'],
            'totalStudents' => $summaryMetrics['totalStudents'],
            'uniqueClasses' => $summaryMetrics['uniqueClasses'],
            'completedSessions' => $summaryMetrics['completedSessions'],
            'completedPercentage' => $summaryMetrics['completedPercentage'],
            'adaSessions' => $summaryMetrics['adaSessions'],
            'adaPercentage' => $summaryMetrics['adaPercentage'],
            'genaiSessions' => $summaryMetrics['genaiSessions'],
            'genaiPercentage' => $summaryMetrics['genaiPercentage'],
            'avgClassSize' => $summaryMetrics['avgClassSize'],
            'avgDuration' => $summaryMetrics['avgDuration'],
        ]);
    }
}
