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

class SummaryStatistics extends Component
{
    // Filter properties (match Dashboard and DetailedExport exactly)
    public $activeTab = 'institutional'; // 'institutional' | 'custom'
    public $viewMode = 'year'; // 'year' | 'fiscal_year' | 'term' | 'month' | 'day'
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

    // Service dependencies
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
        // Default to current academic year
        $availableYears = $this->statisticsService->getAvailableAcademicYears();
        $this->startPeriod = array_key_first($availableYears);
        $this->endPeriod = array_key_last($availableYears);
    }

    public function updatedViewMode($value)
    {
        // Reset start/end periods when view mode changes to prevent data type mismatches
        if ($value === 'year' || $value === 'fiscal_year') {
            // Reset to academic years
            $availableYears = $this->statisticsService->getAvailableAcademicYears();
            $this->startPeriod = array_key_first($availableYears);
            $this->endPeriod = array_key_last($availableYears);
        } elseif ($value === 'term') {
            // Reset to first and last terms
            $terms = $this->availableTerms;
            if ($terms->isNotEmpty()) {
                $this->startPeriod = $terms->first()->id;
                $this->endPeriod = $terms->last()->id;
            }
        } elseif ($value === 'month') {
            // Reset to current month
            $this->startPeriod = now()->format('Y-m');
            $this->endPeriod = now()->format('Y-m');
        } elseif ($value === 'day') {
            // Reset to current date range (30 days)
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

    public function getFilteredInstructorsProperty()
    {
        return $this->statisticsService->getFilteredInstructors($this->instructorSearch);
    }

    public function getFilteredDepartmentsProperty()
    {
        return $this->statisticsService->getFilteredDepartments($this->departmentSearch);
    }

    public function getFilteredClassesProperty()
    {
        return $this->statisticsService->getFilteredClasses($this->classSearch);
    }

    public function updatedInstructorSearch()
    {
        $filteredInstructors = $this->filteredInstructors;
        $this->dispatch('instructors-updated', $filteredInstructors->toArray());
    }

    public function getLibrarians()
    {
        return $this->statisticsService->getLibrarians();
    }

    /**
     * Calculate key metrics for the selected period and filters
     */
    public function getKeyMetricsProperty()
    {
        // Convert filter periods to date range
        [$startDate, $endDate] = $this->statisticsService->convertPeriodsToDateRange(
            $this->viewMode,
            $this->startPeriod,
            $this->endPeriod
        );

        // Build filters array
        $filters = [
            'campus' => $this->campus,
            'department' => $this->department,
            'class' => $this->class,
            'instructor' => $this->instructor,
            'assigned_librarian' => $this->assignedLibrarian,
        ];

        // Calculate metrics using StatisticsService
        // Pass as single "column" representing the entire period
        $metrics = $this->statisticsService->getSummaryMetrics([
            ['start' => $startDate, 'end' => $endDate]
        ], $filters);

        return $metrics;
    }

    /**
     * Calculate modality breakdown (on-campus, remote, asynchronous)
     */
    public function getModalityBreakdownProperty()
    {
        // Convert filter periods to date range
        [$startDate, $endDate] = $this->statisticsService->convertPeriodsToDateRange(
            $this->viewMode,
            $this->startPeriod,
            $this->endPeriod
        );

        // Build filters array
        $filters = [
            'campus' => $this->campus,
            'department' => $this->department,
            'class' => $this->class,
            'instructor' => $this->instructor,
            'assigned_librarian' => $this->assignedLibrarian,
        ];

        // Get breakdown by instruction type
        $breakdown = DB::table('instruction_requests')
            ->leftJoin('instruction_request_details',
                'instruction_requests.id', '=',
                'instruction_request_details.instruction_requests_id')
            ->selectRaw("
                instruction_requests.instruction_type,
                COUNT(*) as sessions,
                SUM(instruction_requests.number_of_students) as students
            ")
            ->whereBetween('instruction_request_details.instruction_datetime', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59'
            ]);

        // Apply filters
        if ($filters['campus']) {
            $breakdown->where('instruction_requests.campus_id', $filters['campus']);
        }
        if ($filters['department']) {
            $breakdown->where('instruction_requests.department', $filters['department']);
        }
        if ($filters['class']) {
            $parts = explode('-', $filters['class']);
            if (count($parts) === 2) {
                $breakdown->where('instruction_requests.department', $parts[0])
                         ->where('instruction_requests.course_number', $parts[1]);
            }
        }
        if ($filters['instructor']) {
            $breakdown->where('instruction_requests.instructor_id', $filters['instructor']);
        }
        if ($filters['assigned_librarian']) {
            $breakdown->where('instruction_request_details.assigned_librarian_id', $filters['assigned_librarian']);
        }

        $results = $breakdown->groupBy('instruction_requests.instruction_type')
            ->get();

        // Calculate totals for percentages
        $totalSessions = $results->sum('sessions');
        $totalStudents = $results->sum('students');

        // Format results with proper labels and calculations
        $formatted = [];
        foreach ($results as $row) {
            $sessions = $row->sessions;
            $students = $row->students ?? 0;
            $avgClassSize = $sessions > 0 ? round($students / $sessions, 1) : 0;
            $percentage = $totalSessions > 0 ? round(($sessions / $totalSessions) * 100, 1) : 0;

            // Convert instruction_type to display label
            $label = match($row->instruction_type) {
                'on-campus' => 'On-Campus',
                'remote' => 'Remote',
                'asynchronous' => 'Asynchronous',
                default => ucfirst($row->instruction_type)
            };

            $formatted[] = [
                'modality' => $label,
                'sessions' => $sessions,
                'students' => $students,
                'avg_class_size' => $avgClassSize,
                'percentage' => $percentage,
            ];
        }

        // Sort by sessions descending
        usort($formatted, fn($a, $b) => $b['sessions'] <=> $a['sessions']);

        return $formatted;
    }

    /**
     * Calculate department breakdown
     */
    public function getDepartmentBreakdownProperty()
    {
        // Convert filter periods to date range
        [$startDate, $endDate] = $this->statisticsService->convertPeriodsToDateRange(
            $this->viewMode,
            $this->startPeriod,
            $this->endPeriod
        );

        // Build filters array
        $filters = [
            'campus' => $this->campus,
            'department' => $this->department,
            'class' => $this->class,
            'instructor' => $this->instructor,
            'assigned_librarian' => $this->assignedLibrarian,
        ];

        // Get breakdown by department
        $breakdown = DB::table('instruction_requests')
            ->leftJoin('instruction_request_details',
                'instruction_requests.id', '=',
                'instruction_request_details.instruction_requests_id')
            ->selectRaw("
                instruction_requests.department,
                COUNT(*) as sessions,
                SUM(instruction_requests.number_of_students) as students
            ")
            ->whereBetween('instruction_request_details.instruction_datetime', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59'
            ])
            ->whereNotNull('instruction_requests.department')
            ->where('instruction_requests.department', '!=', '');

        // Apply filters
        if ($filters['campus']) {
            $breakdown->where('instruction_requests.campus_id', $filters['campus']);
        }
        if ($filters['department']) {
            $breakdown->where('instruction_requests.department', $filters['department']);
        }
        if ($filters['class']) {
            $parts = explode('-', $filters['class']);
            if (count($parts) === 2) {
                $breakdown->where('instruction_requests.department', $parts[0])
                         ->where('instruction_requests.course_number', $parts[1]);
            }
        }
        if ($filters['instructor']) {
            $breakdown->where('instruction_requests.instructor_id', $filters['instructor']);
        }
        if ($filters['assigned_librarian']) {
            $breakdown->where('instruction_request_details.assigned_librarian_id', $filters['assigned_librarian']);
        }

        $results = $breakdown->groupBy('instruction_requests.department')
            ->get();

        // Calculate totals for percentages
        $totalSessions = $results->sum('sessions');
        $totalStudents = $results->sum('students');

        // Get department names from service
        $departmentNames = $this->departmentService->getAllDepartments();

        // Format results with proper labels and calculations
        $formatted = [];
        foreach ($results as $row) {
            $sessions = $row->sessions;
            $students = $row->students ?? 0;
            $avgClassSize = $sessions > 0 ? round($students / $sessions, 1) : 0;
            $percentage = $totalSessions > 0 ? round(($sessions / $totalSessions) * 100, 1) : 0;

            // Get full department name or use code if not found
            $departmentName = $departmentNames[$row->department] ?? $row->department;

            $formatted[] = [
                'department' => $departmentName,
                'sessions' => $sessions,
                'students' => $students,
                'avg_class_size' => $avgClassSize,
                'percentage' => $percentage,
            ];
        }

        // Sort by sessions descending
        usort($formatted, fn($a, $b) => $b['sessions'] <=> $a['sessions']);

        return $formatted;
    }

    public function applyFilters()
    {
        // Placeholder for future filter application logic
        $this->dispatch('filtersApplied');
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
        $this->instructorSearch = '';

        $this->dispatch('clear-instructor');
    }

    public function removeFilter($filterName)
    {
        $this->$filterName = null;

        if ($filterName === 'instructor') {
            $this->dispatch('clear-instructor');
        }
    }

    /**
     * Get period label for export header
     */
    protected function getPeriodLabel(): string
    {
        $start = $this->startPeriod;
        $end = $this->endPeriod;

        return match($this->viewMode) {
            'year' => "AY {$start}-" . ($start + 1) . " to AY {$end}-" . ($end + 1),
            'fiscal_year' => "FY {$start}-" . ($start + 1) . " to FY {$end}-" . ($end + 1),
            'term' => $this->getTermLabel($start, $end),
            'month', 'day' => $start . ' to ' . $end,
            default => 'Unknown Period'
        };
    }

    /**
     * Get term label for period display
     */
    protected function getTermLabel($startTermId, $endTermId): string
    {
        $startTerm = AcademicTerm::find($startTermId);
        $endTerm = AcademicTerm::find($endTermId);

        if (!$startTerm || !$endTerm) {
            return 'Unknown Terms';
        }

        if ($startTermId === $endTermId) {
            return $startTerm->name;
        }

        return $startTerm->name . ' to ' . $endTerm->name;
    }

    /**
     * Export summary statistics to Excel
     */
    public function exportExcel()
    {
        $filename = 'summary_statistics_' . now()->format('Y-m-d_His') . '.xlsx';
        $filePath = storage_path('app/' . $filename);

        $writer = new XLSXWriter();
        $writer->openToFile($filePath);

        // Header Section
        $writer->addRow(Row::fromValues(['Portland Community College - Library Instruction Summary Statistics']));
        $writer->addRow(Row::fromValues(['Generated: ' . now()->format('F j, Y g:i A')]));
        $writer->addRow(Row::fromValues(['Period: ' . $this->getPeriodLabel()]));

        // Active filters
        $activeFilters = [];
        if ($this->campus) {
            $campus = $this->getCampuses()->find($this->campus);
            $activeFilters[] = 'Campus: ' . ($campus ? $campus->name : 'Unknown');
        }
        if ($this->department) {
            $departments = $this->getDepartments();
            $activeFilters[] = 'Department: ' . ($departments[$this->department] ?? $this->department);
        }
        if ($this->instructor) {
            $instructor = $this->getInstructors()->find($this->instructor);
            $activeFilters[] = 'Instructor: ' . ($instructor ? $instructor->display_name : 'Unknown');
        }
        if ($this->assignedLibrarian) {
            $librarian = $this->getLibrarians()->find($this->assignedLibrarian);
            $activeFilters[] = 'Librarian: ' . ($librarian ? $librarian->display_name : 'Unknown');
        }

        if (!empty($activeFilters)) {
            $writer->addRow(Row::fromValues(['Filters: ' . implode(', ', $activeFilters)]));
        }

        $writer->addRow(Row::fromValues([''])); // Empty row

        // Key Metrics Section
        $writer->addRow(Row::fromValues(['KEY METRICS']));
        $writer->addRow(Row::fromValues(['Metric', 'Value']));
        $metrics = $this->keyMetrics;
        $writer->addRow(Row::fromValues(['Total Sessions', number_format($metrics['totalSessions'])]));
        $writer->addRow(Row::fromValues(['Total Students', number_format($metrics['totalStudents'])]));
        $writer->addRow(Row::fromValues(['Unique Classes', number_format($metrics['uniqueClasses'])]));
        $writer->addRow(Row::fromValues(['Average Class Size', number_format($metrics['averageClassSize'], 1)]));
        $writer->addRow(Row::fromValues(['Total Instruction Hours', number_format($metrics['totalInstructionHours'], 1)]));
        $writer->addRow(Row::fromValues(['Completed Sessions', number_format($metrics['completedSessions']) . ' (' . number_format($metrics['completedPercentage'], 1) . '%)']));
        $writer->addRow(Row::fromValues(['ADA Sessions', number_format($metrics['adaSessions']) . ' (' . number_format($metrics['adaPercentage'], 1) . '%)']));
        $writer->addRow(Row::fromValues(['GenAI Interest Sessions', number_format($metrics['genAISessions']) . ' (' . number_format($metrics['genAIPercentage'], 1) . '%)']));
        $writer->addRow(Row::fromValues([''])); // Empty row

        // Modality Breakdown Section
        $writer->addRow(Row::fromValues(['MODALITY BREAKDOWN']));
        $writer->addRow(Row::fromValues(['Modality', 'Sessions', 'Students', 'Avg Class Size', '% of Total']));
        foreach ($this->modalityBreakdown as $row) {
            $writer->addRow(Row::fromValues([
                $row['modality'],
                $row['sessions'],
                $row['students'],
                number_format($row['avg_class_size'], 1),
                number_format($row['percentage'], 1) . '%'
            ]));
        }
        $writer->addRow(Row::fromValues([''])); // Empty row

        // Department Breakdown Section
        $writer->addRow(Row::fromValues(['DEPARTMENT BREAKDOWN']));
        $writer->addRow(Row::fromValues(['Department', 'Sessions', 'Students', 'Avg Class Size', '% of Total']));
        foreach ($this->departmentBreakdown as $row) {
            $writer->addRow(Row::fromValues([
                $row['department'],
                $row['sessions'],
                $row['students'],
                number_format($row['avg_class_size'], 1),
                number_format($row['percentage'], 1) . '%'
            ]));
        }
        $writer->addRow(Row::fromValues([''])); // Empty row

        // Footer
        $writer->addRow(Row::fromValues(['Report generated by Library Instruction Request System']));
        $writer->addRow(Row::fromValues(['Data reflects all instruction sessions within the specified period and filters']));

        $writer->close();

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    /**
     * Export summary statistics to CSV
     */
    public function exportCsv()
    {
        $filename = 'summary_statistics_' . now()->format('Y-m-d_His') . '.csv';
        $filePath = storage_path('app/' . $filename);

        $writer = new CSVWriter();
        $writer->openToFile($filePath);

        // Header Section
        $writer->addRow(Row::fromValues(['Portland Community College - Library Instruction Summary Statistics']));
        $writer->addRow(Row::fromValues(['Generated: ' . now()->format('F j, Y g:i A')]));
        $writer->addRow(Row::fromValues(['Period: ' . $this->getPeriodLabel()]));

        // Active filters
        $activeFilters = [];
        if ($this->campus) {
            $campus = $this->getCampuses()->find($this->campus);
            $activeFilters[] = 'Campus: ' . ($campus ? $campus->name : 'Unknown');
        }
        if ($this->department) {
            $departments = $this->getDepartments();
            $activeFilters[] = 'Department: ' . ($departments[$this->department] ?? $this->department);
        }
        if ($this->instructor) {
            $instructor = $this->getInstructors()->find($this->instructor);
            $activeFilters[] = 'Instructor: ' . ($instructor ? $instructor->display_name : 'Unknown');
        }
        if ($this->assignedLibrarian) {
            $librarian = $this->getLibrarians()->find($this->assignedLibrarian);
            $activeFilters[] = 'Librarian: ' . ($librarian ? $librarian->display_name : 'Unknown');
        }

        if (!empty($activeFilters)) {
            $writer->addRow(Row::fromValues(['Filters: ' . implode(', ', $activeFilters)]));
        }

        $writer->addRow(Row::fromValues([''])); // Empty row

        // Key Metrics Section
        $writer->addRow(Row::fromValues(['KEY METRICS']));
        $writer->addRow(Row::fromValues(['Metric', 'Value']));
        $metrics = $this->keyMetrics;
        $writer->addRow(Row::fromValues(['Total Sessions', number_format($metrics['totalSessions'])]));
        $writer->addRow(Row::fromValues(['Total Students', number_format($metrics['totalStudents'])]));
        $writer->addRow(Row::fromValues(['Unique Classes', number_format($metrics['uniqueClasses'])]));
        $writer->addRow(Row::fromValues(['Average Class Size', number_format($metrics['averageClassSize'], 1)]));
        $writer->addRow(Row::fromValues(['Total Instruction Hours', number_format($metrics['totalInstructionHours'], 1)]));
        $writer->addRow(Row::fromValues(['Completed Sessions', number_format($metrics['completedSessions']) . ' (' . number_format($metrics['completedPercentage'], 1) . '%)']));
        $writer->addRow(Row::fromValues(['ADA Sessions', number_format($metrics['adaSessions']) . ' (' . number_format($metrics['adaPercentage'], 1) . '%)']));
        $writer->addRow(Row::fromValues(['GenAI Interest Sessions', number_format($metrics['genAISessions']) . ' (' . number_format($metrics['genAIPercentage'], 1) . '%)']));
        $writer->addRow(Row::fromValues([''])); // Empty row

        // Modality Breakdown Section
        $writer->addRow(Row::fromValues(['MODALITY BREAKDOWN']));
        $writer->addRow(Row::fromValues(['Modality', 'Sessions', 'Students', 'Avg Class Size', '% of Total']));
        foreach ($this->modalityBreakdown as $row) {
            $writer->addRow(Row::fromValues([
                $row['modality'],
                $row['sessions'],
                $row['students'],
                number_format($row['avg_class_size'], 1),
                number_format($row['percentage'], 1) . '%'
            ]));
        }
        $writer->addRow(Row::fromValues([''])); // Empty row

        // Department Breakdown Section
        $writer->addRow(Row::fromValues(['DEPARTMENT BREAKDOWN']));
        $writer->addRow(Row::fromValues(['Department', 'Sessions', 'Students', 'Avg Class Size', '% of Total']));
        foreach ($this->departmentBreakdown as $row) {
            $writer->addRow(Row::fromValues([
                $row['department'],
                $row['sessions'],
                $row['students'],
                number_format($row['avg_class_size'], 1),
                number_format($row['percentage'], 1) . '%'
            ]));
        }
        $writer->addRow(Row::fromValues([''])); // Empty row

        // Footer
        $writer->addRow(Row::fromValues(['Report generated by Library Instruction Request System']));
        $writer->addRow(Row::fromValues(['Data reflects all instruction sessions within the specified period and filters']));

        $writer->close();

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function render()
    {
        return view('livewire.statistics.summary-statistics', [
            'availableAcademicYears' => $this->getAvailableAcademicYears(),
            'availableTerms' => $this->availableTerms,
            'campuses' => $this->getCampuses(),
            'departments' => $this->getDepartments(),
            'instructors' => $this->getInstructors(),
            'librarians' => $this->getLibrarians(),
            'keyMetrics' => $this->keyMetrics,
            'modalityBreakdown' => $this->modalityBreakdown,
            'departmentBreakdown' => $this->departmentBreakdown,
        ]);
    }
}
