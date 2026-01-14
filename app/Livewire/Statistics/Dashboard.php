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
use OpenSpout\Writer\XLSX\Writer as XLSXWriter;
use OpenSpout\Writer\CSV\Writer as CSVWriter;
use OpenSpout\Common\Entity\Row;

class Dashboard extends Component
{
    public $activeTab = 'institutional'; // 'institutional' | 'custom'
    public $viewMode = 'year';
    public $startPeriod;
    public $endPeriod;
    public $campus = null;
    public $department = null;
    public $class = null;
    public $instructor = null;
    public $assignedLibrarian = null;
    public $instructorSearch = '';
    public $departmentSearch = '';
    public $classSearch = '';
    public $showTruncationWarning = false;
    public $truncationMessage = '';
    public $filtersExpanded = true;

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

    public function getLibrarians()
    {
        return $this->statisticsService->getLibrarians();
    }

    public function applyFilters()
    {
        $this->showTruncationWarning = false;
        $this->truncationMessage = '';
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
        $this->showTruncationWarning = false;
        $this->truncationMessage = '';

        $this->dispatch('clear-instructor');
    }

    public function removeFilter($filterName)
    {
        $this->$filterName = null;

        if ($filterName === 'instructor') {
            $this->dispatch('clear-instructor');
        }
    }

    public function getColumns(): array
    {
        $start = $this->startPeriod;
        $end = $this->endPeriod ?? ($this->viewMode === 'year' ? $this->getCurrentAcademicYear() : now()->format('Y-m-d'));

        switch ($this->viewMode) {
            case 'year':
                return $this->getYearColumns($start, $end);
            case 'fiscal_year':
                return $this->getFiscalYearColumns($start, $end);
            case 'term':
                return $this->getTermColumns($start, $end);
            case 'month':
                return $this->getMonthColumns($start, $end);
            case 'day':
                return $this->getDayColumns($start, $end);
            default:
                return [];
        }
    }

    protected function getYearColumns($startAY, $endAY): array
    {
        $columns = [];
        $years = range($startAY, $endAY);

        if (count($years) > 5) {
            $years = array_slice($years, -5);
            $this->showTruncationWarning = true;
            $this->truncationMessage = "Showing last 5 academic years of selected range (" . $years[0] . "-" . ($years[4] + 1) . ")";
        }

        foreach ($years as $ay) {
            $columns[] = [
                'key' => "ay_$ay",
                'label' => "$ay-" . ($ay + 1),
                'start' => "$ay-09-01",
                'end' => ($ay + 1) . "-08-31"
            ];
        }

        return $columns;
    }

    protected function getFiscalYearColumns($startFY, $endFY): array
    {
        $columns = [];
        $years = range($startFY, $endFY);

        if (count($years) > 5) {
            $years = array_slice($years, -5);
            $this->showTruncationWarning = true;
            $this->truncationMessage = "Showing last 5 fiscal years of selected range (FY " . $years[0] . "-" . ($years[4] + 1) . ")";
        }

        foreach ($years as $fy) {
            $columns[] = [
                'key' => "fy_$fy",
                'label' => "FY $fy-" . ($fy + 1),
                'start' => "$fy-07-01",
                'end' => ($fy + 1) . "-06-30"
            ];
        }

        return $columns;
    }

    protected function getTermColumns($startTermId, $endTermId): array
    {
        $columns = [];

        // Get start and end terms
        $startTerm = AcademicTerm::find($startTermId);
        $endTerm = AcademicTerm::find($endTermId);

        if (!$startTerm || !$endTerm) {
            return [];
        }

        // Get all terms between start_date and end_date (inclusive)
        $terms = AcademicTerm::where('start_date', '>=', $startTerm->start_date)
            ->where('start_date', '<=', $endTerm->end_date)
            ->orderBy('start_date')
            ->get();

        if ($terms->count() > 8) {
            $terms = $terms->slice(-8);
            $this->showTruncationWarning = true;
            $this->truncationMessage = "Showing last 8 terms of selected range";
        }

        foreach ($terms as $term) {
            $yearPart = explode('-', $term->academic_year)[0];
            $columns[] = [
                'key' => "{$term->term_name}_{$term->academic_year}",
                'label' => ucfirst($term->term_name) . ' ' . $yearPart,
                'start' => $term->start_date->format('Y-m-d'),
                'end' => $term->end_date->format('Y-m-d'),
            ];
        }

        return $columns;
    }

    protected function getMonthColumns($startMonth, $endMonth): array
    {
        $columns = [];
        $start = \Carbon\Carbon::parse($startMonth . '-01');
        $end = \Carbon\Carbon::parse(($endMonth ?? now()->format('Y-m')) . '-01');

        if ($start->greaterThan($end)) {
            return $columns;
        }

        $months = [];
        $current = $start->copy();
        while ($current->lessThanOrEqualTo($end)) {
            $months[] = $current->copy();
            $current->addMonth();
        }

        if (count($months) > 12) {
            $months = array_slice($months, -12);
            $this->showTruncationWarning = true;
            $this->truncationMessage = "Showing last 12 months of selected range";
        }

        foreach ($months as $month) {
            $columns[] = [
                'key' => 'month_' . $month->format('Y_m'),
                'label' => $month->format('M-y'),
                'start' => $month->startOfMonth()->format('Y-m-d'),
                'end' => $month->copy()->endOfMonth()->format('Y-m-d')
            ];
        }

        return $columns;
    }

    protected function getDayColumns($startDate, $endDate): array
    {
        $columns = [];
        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate ?? now()->format('Y-m-d'));

        if ($start->greaterThan($end)) {
            return $columns;
        }

        $days = [];
        $current = $start->copy();
        while ($current->lessThanOrEqualTo($end) && count($days) < 31) {
            $days[] = $current->copy();
            $current->addDay();
        }

        if ($end->diffInDays($start) > 30) {
            $this->showTruncationWarning = true;
            $this->truncationMessage = "Showing last 31 days of selected range";
        }

        foreach ($days as $day) {
            $columns[] = [
                'key' => 'day_' . $day->format('Y_m_d'),
                'label' => $day->format('m/d/y'),
                'start' => $day->format('Y-m-d'),
                'end' => $day->format('Y-m-d')
            ];
        }

        return $columns;
    }

    public function getStatisticsData(): array
    {
        $columns = $this->getColumns();

        $metrics = [
            'synchronous' => ['label' => 'Synchronous', 'calculation' => 'count_sync'],
            'asynchronous' => ['label' => 'Asynchronous', 'calculation' => 'count_async'],
            'attendance' => ['label' => 'Attendance (Students)', 'calculation' => 'sum_students'],
            'remote' => ['label' => 'Remote', 'calculation' => 'count_remote'],
            'in_person' => ['label' => 'In Person', 'calculation' => 'count_oncampus'],
            'ada' => ['label' => 'ADA', 'calculation' => 'count_ada'],
            'no_ada' => ['label' => 'No ADA', 'calculation' => 'count_no_ada']
        ];

        $data = [];
        foreach ($metrics as $key => $metric) {
            $row = ['category' => $metric['label']];

            foreach ($columns as $column) {
                $value = $this->calculateMetric($metric['calculation'], $column['start'], $column['end']);
                $row[$column['key']] = $value;
            }

            $data[] = $row;
        }

        return $data;
    }

    protected function calculateMetric($calculation, $startDate, $endDate): int
    {
        // Map Dashboard calculation codes to service metric names
        $metricMap = [
            'count_sync' => 'synchronous',
            'count_async' => 'asynchronous',
            'sum_students' => 'total_students',
            'count_remote' => 'remote',
            'count_oncampus' => 'on_campus',
            'count_ada' => 'ada',
            'count_no_ada' => 'no_ada',
        ];

        $metric = $metricMap[$calculation] ?? null;
        if (!$metric) {
            return 0;
        }

        // Build filters array from Dashboard properties
        $filters = [
            'campus' => $this->campus,
            'department' => $this->department,
            'class' => $this->class,
            'instructor' => $this->instructor,
            'assigned_librarian' => $this->assignedLibrarian,
        ];

        return $this->statisticsService->calculateMetric($metric, $startDate, $endDate, $filters);
    }

    public function exportCsv()
    {
        $columns = $this->getColumns();
        $data = $this->getStatisticsData();

        $filename = 'instruction_statistics_' . now()->format('Y-m-d') . '.csv';
        $filePath = storage_path('app/' . $filename);

        $writer = new CSVWriter();
        $writer->openToFile($filePath);

        $headerRow = ['Category'];
        foreach ($columns as $column) {
            $headerRow[] = $column['label'];
        }
        $writer->addRow(Row::fromValues($headerRow));

        foreach ($data as $row) {
            $rowData = [$row['category']];
            foreach ($columns as $column) {
                $rowData[] = $row[$column['key']] ?? 0;
            }
            $writer->addRow(Row::fromValues($rowData));
        }

        $writer->close();

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function exportExcel()
    {
        $columns = $this->getColumns();
        $data = $this->getStatisticsData();

        $filename = 'instruction_statistics_' . now()->format('Y-m-d') . '.xlsx';
        $filePath = storage_path('app/' . $filename);

        $writer = new XLSXWriter();
        $writer->openToFile($filePath);

        $headerRow = ['Category'];
        foreach ($columns as $column) {
            $headerRow[] = $column['label'];
        }
        $writer->addRow(Row::fromValues($headerRow));

        foreach ($data as $row) {
            $rowData = [$row['category']];
            foreach ($columns as $column) {
                $rowData[] = $row[$column['key']] ?? 0;
            }
            $writer->addRow(Row::fromValues($rowData));
        }

        $writer->close();

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function render()
    {
        $columns = $this->getColumns();
        $statisticsData = $this->getStatisticsData();
        $totalSessions = 0;
        $totalStudents = 0;

        if (!empty($statisticsData)) {
            foreach ($columns as $column) {
                $totalSessions += $statisticsData[0][$column['key']] ?? 0;
                $totalStudents += $statisticsData[2][$column['key']] ?? 0;
            }
        }

        $summaryMetrics = $this->calculateSummaryMetrics($columns);

        return view('livewire.statistics.dashboard', [
            'availableAcademicYears' => $this->getAvailableAcademicYears(),
            'availableTerms' => $this->availableTerms,
            'campuses' => $this->getCampuses(),
            'departments' => $this->getDepartments(),
            'instructors' => $this->getInstructors(),
            'librarians' => $this->getLibrarians(),
            'columns' => $columns,
            'statisticsData' => $statisticsData,
            'totalSessions' => $totalSessions,
            'totalStudents' => $totalStudents,
            'totalInstructionHours' => $summaryMetrics['totalInstructionHours'],
            'averageClassSize' => $summaryMetrics['averageClassSize'],
            'adaSessions' => $summaryMetrics['adaSessions'],
            'adaPercentage' => $summaryMetrics['adaPercentage'],
            'scheduledAndCompleted' => $summaryMetrics['scheduledAndCompleted'],
            'completedOnly' => $summaryMetrics['completedOnly'],
            'avgSessionDuration' => $summaryMetrics['avgSessionDuration']
        ]);
    }

    protected function calculateSummaryMetrics(array $columns): array
    {
        // Build filters array from Dashboard properties
        $filters = [
            'campus' => $this->campus,
            'department' => $this->department,
            'class' => $this->class,
            'instructor' => $this->instructor,
            'assigned_librarian' => $this->assignedLibrarian,
        ];

        return $this->statisticsService->getSummaryMetrics($columns, $filters);
    }
}
