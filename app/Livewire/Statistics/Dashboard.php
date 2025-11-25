<?php

namespace App\Livewire\Statistics;

use Livewire\Component;
use App\Models\InstructionRequests;
use App\Models\Campus;
use App\Models\Instructor;
use App\Models\User;
use App\Services\DepartmentService;
use Illuminate\Support\Facades\DB;
use OpenSpout\Writer\XLSX\Writer as XLSXWriter;
use OpenSpout\Writer\CSV\Writer as CSVWriter;
use OpenSpout\Common\Entity\Row;

class Dashboard extends Component
{
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

    public function boot(DepartmentService $departmentService)
    {
        $this->departmentService = $departmentService;
    }

    public function mount()
    {
        $currentFiscalYear = $this->getCurrentFiscalYear();
        $this->startPeriod = $currentFiscalYear;
        $this->endPeriod = $currentFiscalYear;
    }

    public function getCurrentFiscalYear(): int
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;
        return $currentMonth >= 7 ? $currentYear : $currentYear - 1;
    }

    public function getAvailableFiscalYears(): array
    {
        $minDate = DB::table('instruction_request_details')
            ->whereNotNull('instruction_datetime')
            ->min('instruction_datetime');

        $maxDate = DB::table('instruction_request_details')
            ->whereNotNull('instruction_datetime')
            ->max('instruction_datetime');

        if (!$minDate || !$maxDate) {
            $currentFY = $this->getCurrentFiscalYear();
            return [$currentFY => "$currentFY-" . ($currentFY + 1)];
        }

        $minYear = (int) date('Y', strtotime($minDate));
        $minMonth = (int) date('m', strtotime($minDate));
        $minFY = $minMonth >= 7 ? $minYear : $minYear - 1;

        $maxYear = (int) date('Y', strtotime($maxDate));
        $maxMonth = (int) date('m', strtotime($maxDate));
        $maxFY = $maxMonth >= 7 ? $maxYear : $maxYear - 1;

        $fiscalYears = [];
        for ($fy = $maxFY; $fy >= $minFY; $fy--) {
            $fiscalYears[$fy] = "$fy-" . ($fy + 1);
        }

        return $fiscalYears;
    }

    public function getCampuses()
    {
        return Campus::ordered()->get();
    }

    public function getDepartments()
    {
        return $this->departmentService->getAllDepartments();
    }

    public function getInstructors()
    {
        return Instructor::orderBy('display_name')->get();
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
        // Get filtered instructors
        $filteredInstructors = $this->filteredInstructors;

        // Dispatch event with the filtered instructors
        $this->dispatch('instructors-updated', $filteredInstructors->toArray());
    }

    public function getLibrarians()
    {
        return User::where('is_admin', false)
            ->orderBy('display_name')
            ->get();
    }

    public function applyFilters()
    {
        $this->showTruncationWarning = false;
        $this->truncationMessage = '';
        $this->dispatch('filtersApplied');
    }

    public function clearFilters()
    {
        $currentFiscalYear = $this->getCurrentFiscalYear();
        $this->viewMode = 'year';
        $this->startPeriod = $currentFiscalYear;
        $this->endPeriod = $currentFiscalYear;
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
        $end = $this->endPeriod ?? ($this->viewMode === 'year' ? $this->getCurrentFiscalYear() : now()->format('Y-m-d'));

        switch ($this->viewMode) {
            case 'year':
                return $this->getYearColumns($start, $end);
            case 'month':
                return $this->getMonthColumns($start, $end);
            case 'day':
                return $this->getDayColumns($start, $end);
            default:
                return [];
        }
    }

    protected function getYearColumns($startFY, $endFY): array
    {
        $columns = [];
        $years = range($startFY, $endFY);

        if (count($years) > 5) {
            $years = array_slice($years, -5);
            $this->showTruncationWarning = true;
            $this->truncationMessage = "Showing last 5 fiscal years of selected range (" . $years[0] . "-" . ($years[4] + 1) . ")";
        }

        foreach ($years as $fy) {
            $columns[] = [
                'key' => "fy_$fy",
                'label' => "$fy-" . ($fy + 1),
                'start' => "$fy-07-01",
                'end' => ($fy + 1) . "-06-30"
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
        $query = InstructionRequests::query()
            ->leftJoin('instruction_request_details',
                'instruction_requests.id', '=',
                'instruction_request_details.instruction_requests_id')
            ->whereBetween('instruction_request_details.instruction_datetime', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->when($this->campus, fn($q) => $q->where('instruction_requests.campus_id', $this->campus))
            ->when($this->department, fn($q) => $q->where('instruction_requests.department', $this->department))
            ->when($this->class, function($q) {
                $parts = explode('-', $this->class);
                if (count($parts) === 2) {
                    return $q->where('instruction_requests.department', $parts[0])
                             ->where('instruction_requests.course_number', $parts[1]);
                }
                return $q;
            })
            ->when($this->instructor, fn($q) => $q->where('instruction_requests.instructor_id', $this->instructor))
            ->when($this->assignedLibrarian, fn($q) =>
                $q->where('instruction_request_details.assigned_librarian_id', $this->assignedLibrarian));

        switch ($calculation) {
            case 'count_sync':
                return $query->whereIn('instruction_requests.instruction_type', ['on-campus', 'remote'])->count();
            case 'count_async':
                return $query->where('instruction_requests.instruction_type', 'asynchronous')->count();
            case 'sum_students':
                return $query->sum('instruction_requests.number_of_students') ?? 0;
            case 'count_remote':
                return $query->where('instruction_requests.instruction_type', 'remote')->count();
            case 'count_oncampus':
                return $query->where('instruction_requests.instruction_type', 'on-campus')->count();
            case 'count_ada':
                return $query->where('instruction_requests.ada_provisions_needed', true)->count();
            case 'count_no_ada':
                return $query->where('instruction_requests.ada_provisions_needed', false)->count();
            default:
                return 0;
        }
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
            'availableFiscalYears' => $this->getAvailableFiscalYears(),
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
        $totalMinutes = 0;
        $totalStudents = 0;
        $totalSessions = 0;
        $adaSessions = 0;
        $scheduledAndCompleted = 0;
        $completedOnly = 0;

        foreach ($columns as $column) {
            $query = InstructionRequests::query()
                ->leftJoin('instruction_request_details',
                    'instruction_requests.id', '=',
                    'instruction_request_details.instruction_requests_id')
                ->whereBetween('instruction_request_details.instruction_datetime',
                    [$column['start'] . ' 00:00:00', $column['end'] . ' 23:59:59'])
                ->when($this->campus, fn($q) => $q->where('instruction_requests.campus_id', $this->campus))
                ->when($this->department, fn($q) => $q->where('instruction_requests.department', $this->department))
                ->when($this->class, function($q) {
                    $parts = explode('-', $this->class);
                    if (count($parts) === 2) {
                        return $q->where('instruction_requests.department', $parts[0])
                                 ->where('instruction_requests.course_number', $parts[1]);
                    }
                    return $q;
                })
                ->when($this->instructor, fn($q) => $q->where('instruction_requests.instructor_id', $this->instructor))
                ->when($this->assignedLibrarian, fn($q) =>
                    $q->where('instruction_request_details.assigned_librarian_id', $this->assignedLibrarian));

            $sessionCount = $query->count();
            $totalSessions += $sessionCount;

            $totalMinutes += $query->sum(DB::raw("CAST(SUBSTRING_INDEX(instruction_requests.duration, ' ', 1) AS UNSIGNED)")) ?? 0;

            $totalStudents += $query->sum('instruction_requests.number_of_students') ?? 0;

            $adaSessions += $query->where('instruction_requests.ada_provisions_needed', true)->count();

            $scheduledAndCompleted += $query->whereIn('instruction_requests.status', ['scheduled', 'completed'])->count();

            $completedOnly += $query->where('instruction_requests.status', 'completed')->count();
        }

        $totalHours = round($totalMinutes / 60, 1);
        $avgClassSize = $totalSessions > 0 ? round($totalStudents / $totalSessions, 1) : 0;
        $adaPercent = $totalSessions > 0 ? round(($adaSessions / $totalSessions) * 100, 1) : 0;
        $avgSessionDuration = $totalSessions > 0 ? round($totalMinutes / $totalSessions) : 0;

        return [
            'totalInstructionHours' => $totalHours,
            'averageClassSize' => $avgClassSize,
            'adaSessions' => $adaSessions,
            'adaPercentage' => $adaPercent,
            'scheduledAndCompleted' => $scheduledAndCompleted,
            'completedOnly' => $completedOnly,
            'avgSessionDuration' => $avgSessionDuration
        ];
    }
}
