<?php

namespace App\Services;

use App\Models\InstructionRequests;
use App\Models\AcademicTerm;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Campus;
use App\Models\Instructor;
use App\Models\User;
use Illuminate\Support\Collection;

class StatisticsService
{
    protected DepartmentService $departmentService;
    protected TermService $termService;

    public function __construct(DepartmentService $departmentService, TermService $termService)
    {
        $this->departmentService = $departmentService;
        $this->termService = $termService;
    }

    /**
     * Get base query with common joins and relationships
     */
    public function getBaseQuery(): Builder
    {
        return InstructionRequests::query()
            ->leftJoin('instruction_request_details',
                'instruction_requests.id', '=',
                'instruction_request_details.instruction_requests_id')
            ->with([
                'instructor',
                'campus',
                'detail.assignedLibrarian',
                'librarian',
            ]);
    }

    /**
     * Apply common filters to query
     *
     * Extracted from Dashboard.php lines 367-379 (calculateMetric method)
     */
    public function applyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['campus'] ?? null, fn($q, $campus) =>
                $q->where('instruction_requests.campus_id', $campus))
            ->when($filters['department'] ?? null, fn($q, $dept) =>
                $q->where('instruction_requests.department', $dept))
            ->when($filters['class'] ?? null, function($q, $class) {
                $parts = explode('-', $class);
                if (count($parts) === 2) {
                    return $q->where('instruction_requests.department', $parts[0])
                             ->where('instruction_requests.course_number', $parts[1]);
                }
                return $q;
            })
            ->when($filters['instructor'] ?? null, fn($q, $instructor) =>
                $q->where('instruction_requests.instructor_id', $instructor))
            ->when($filters['assigned_librarian'] ?? null, fn($q, $librarian) =>
                $q->where('instruction_request_details.assigned_librarian_id', $librarian));
    }

    /**
     * Apply date range filter
     */
    public function applyDateRange(Builder $query, string $startDate, string $endDate): Builder
    {
        return $query->whereBetween(
            'instruction_request_details.instruction_datetime',
            [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59'
            ]
        );
    }

    /**
     * Calculate specific metric for date range
     *
     * Extracted from Dashboard.php calculateMetric() method
     */
    public function calculateMetric(string $metric, string $startDate, string $endDate, array $filters = []): int
    {
        $query = $this->getBaseQuery();
        $query = $this->applyFilters($query, $filters);
        $query = $this->applyDateRange($query, $startDate, $endDate);

        return match($metric) {
            'synchronous' => $query->whereIn('instruction_requests.instruction_type', ['on-campus', 'remote'])->count(),
            'asynchronous' => $query->where('instruction_requests.instruction_type', 'asynchronous')->count(),
            'total_students' => $query->sum('instruction_requests.number_of_students') ?? 0,
            'remote' => $query->where('instruction_requests.instruction_type', 'remote')->count(),
            'on_campus' => $query->where('instruction_requests.instruction_type', 'on-campus')->count(),
            'ada' => $query->where('instruction_requests.ada_provisions_needed', true)->count(),
            'no_ada' => $query->where('instruction_requests.ada_provisions_needed', false)->count(),
            default => 0
        };
    }

    /**
     * Get all metrics for a date range
     */
    public function getAllMetrics(string $startDate, string $endDate, array $filters = []): array
    {
        return [
            'synchronous' => $this->calculateMetric('synchronous', $startDate, $endDate, $filters),
            'asynchronous' => $this->calculateMetric('asynchronous', $startDate, $endDate, $filters),
            'total_students' => $this->calculateMetric('total_students', $startDate, $endDate, $filters),
            'remote' => $this->calculateMetric('remote', $startDate, $endDate, $filters),
            'on_campus' => $this->calculateMetric('on_campus', $startDate, $endDate, $filters),
            'ada' => $this->calculateMetric('ada', $startDate, $endDate, $filters),
            'no_ada' => $this->calculateMetric('no_ada', $startDate, $endDate, $filters),
        ];
    }

    /**
     * Calculate comprehensive summary metrics
     *
     * Extracted from Dashboard.php calculateSummaryMetrics() method
     * Enhanced to include totalSessions, totalStudents, uniqueClasses, and genAI metrics
     */
    public function getSummaryMetrics(array $columns, array $filters = []): array
    {
        $totalMinutes = 0;
        $totalStudents = 0;
        $totalSessions = 0;
        $adaSessions = 0;
        $scheduledAndCompleted = 0;
        $completedOnly = 0;
        $genAISessions = 0;
        $uniqueClasses = collect();

        foreach ($columns as $column) {
            $query = $this->getBaseQuery();
            $query = $this->applyFilters($query, $filters);
            $query = $this->applyDateRange($query, $column['start'], $column['end']);

            $sessionCount = $query->count();
            $totalSessions += $sessionCount;

            $totalMinutes += $query->sum(DB::raw("CAST(SUBSTRING_INDEX(instruction_requests.duration, ' ', 1) AS UNSIGNED)")) ?? 0;
            $totalStudents += $query->sum('instruction_requests.number_of_students') ?? 0;
            $adaSessions += $query->where('instruction_requests.ada_provisions_needed', true)->count();
            $scheduledAndCompleted += $query->whereIn('instruction_requests.status', ['in_progress', 'completed'])->count();
            $completedOnly += $query->where('instruction_requests.status', 'completed')->count();

            // Count sessions with GenAI discussion interest (text field is not null and not empty)
            $genAISessions += $query->whereNotNull('instruction_requests.genai_discussion_interest')
                ->where('instruction_requests.genai_discussion_interest', '!=', '')
                ->count();

            // Collect unique classes (department-course_number combinations)
            $classes = InstructionRequests::query()
                ->leftJoin('instruction_request_details',
                    'instruction_requests.id', '=',
                    'instruction_request_details.instruction_requests_id')
                ->whereNotNull('instruction_requests.department')
                ->whereNotNull('instruction_requests.course_number')
                ->where('instruction_requests.department', '!=', '')
                ->where('instruction_requests.course_number', '!=', '');

            $classes = $this->applyFilters($classes, $filters);
            $classes = $this->applyDateRange($classes, $column['start'], $column['end']);

            $classes->select(DB::raw("DISTINCT CONCAT(UPPER(instruction_requests.department), '-', instruction_requests.course_number) as class_code"))
                ->get()
                ->pluck('class_code')
                ->each(function($classCode) use ($uniqueClasses) {
                    $uniqueClasses->push($classCode);
                });
        }

        $totalHours = round($totalMinutes / 60, 1);
        $avgClassSize = $totalSessions > 0 ? round($totalStudents / $totalSessions, 1) : 0;
        $adaPercent = $totalSessions > 0 ? round(($adaSessions / $totalSessions) * 100, 1) : 0;
        $avgSessionDuration = $totalSessions > 0 ? round($totalMinutes / $totalSessions) : 0;
        $completedPercent = $totalSessions > 0 ? round(($completedOnly / $totalSessions) * 100, 1) : 0;
        $genAIPercent = $totalSessions > 0 ? round(($genAISessions / $totalSessions) * 100, 1) : 0;

        return [
            'totalSessions' => $totalSessions,
            'totalStudents' => $totalStudents,
            'uniqueClasses' => $uniqueClasses->unique()->count(),
            'totalInstructionHours' => $totalHours,
            'averageClassSize' => $avgClassSize,
            'adaSessions' => $adaSessions,
            'adaPercentage' => $adaPercent,
            'completedSessions' => $completedOnly,
            'completedPercentage' => $completedPercent,
            'genAISessions' => $genAISessions,
            'genAIPercentage' => $genAIPercent,
            'scheduledAndCompleted' => $scheduledAndCompleted,
            'avgSessionDuration' => $avgSessionDuration,
        ];
    }

    /**
     * Get current academic year (Sept-Aug)
     *
     * Extracted from Dashboard.php getCurrentAcademicYear()
     */
    public function getCurrentAcademicYear(): int
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;
        return $currentMonth >= 9 ? $currentYear : $currentYear - 1;
    }

    /**
     * Get available academic years from data
     *
     * Extracted from Dashboard.php getAvailableAcademicYears()
     */
    public function getAvailableAcademicYears(): array
    {
        $minDate = DB::table('instruction_request_details')
            ->whereNotNull('instruction_datetime')
            ->min('instruction_datetime');

        $maxDate = DB::table('instruction_request_details')
            ->whereNotNull('instruction_datetime')
            ->max('instruction_datetime');

        if (!$minDate || !$maxDate) {
            $currentAY = $this->getCurrentAcademicYear();
            return [$currentAY => "$currentAY-" . ($currentAY + 1)];
        }

        $minYear = (int) date('Y', strtotime($minDate));
        $minMonth = (int) date('m', strtotime($minDate));
        $minAY = $minMonth >= 9 ? $minYear : $minYear - 1;

        $maxYear = (int) date('Y', strtotime($maxDate));
        $maxMonth = (int) date('m', strtotime($maxDate));
        $maxAY = $maxMonth >= 9 ? $maxYear : $maxYear - 1;

        $academicYears = [];
        for ($ay = $minAY; $ay <= $maxAY; $ay++) {
            $academicYears[$ay] = "$ay-" . ($ay + 1);
        }

        return $academicYears;
    }

    /**
     * Get all campuses ordered by name
     *
     * Extracted from Dashboard.php getCampuses()
     */
    public function getCampuses(): Collection
    {
        return Campus::ordered()->get();
    }

    /**
     * Get all departments
     *
     * Extracted from Dashboard.php getDepartments()
     */
    public function getDepartments(): array
    {
        return $this->departmentService->getAllDepartments();
    }

    /**
     * Get all instructors ordered by display name
     *
     * Extracted from Dashboard.php getInstructors()
     */
    public function getInstructors(): Collection
    {
        return Instructor::orderBy('display_name')->get();
    }

    /**
     * Get all non-admin librarians
     *
     * Extracted from Dashboard.php getLibrarians()
     */
    public function getLibrarians(): Collection
    {
        return User::where('is_admin', false)
            ->orderBy('display_name')
            ->get();
    }

    /**
     * Convert period selections to date range
     *
     * Extracted from DetailedExport.php convertPeriodsToDateRange() method
     * Used by DetailedExport and SummaryStatistics to convert filter periods to actual date ranges
     */
    public function convertPeriodsToDateRange(string $viewMode, $startPeriod, $endPeriod): array
    {
        switch ($viewMode) {
            case 'year':
                $endYear = (int) $endPeriod;
                return [
                    "{$startPeriod}-09-01",
                    ($endYear + 1) . "-08-31"
                ];

            case 'fiscal_year':
                $endYear = (int) $endPeriod;
                return [
                    "{$startPeriod}-07-01",
                    ($endYear + 1) . "-06-30"
                ];

            case 'term':
                $startTerm = AcademicTerm::find($startPeriod);
                $endTerm = AcademicTerm::find($endPeriod);

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
                    $startPeriod . '-01',
                    Carbon::parse($endPeriod)->endOfMonth()->format('Y-m-d')
                ];

            case 'day':
                return [$startPeriod, $endPeriod];

            default:
                return [now()->subDays(30)->format('Y-m-d'), now()->format('Y-m-d')];
        }
    }

    /**
     * Get filtered instructors by search query
     *
     * Extracted from Dashboard.php, DetailedExport.php, and SummaryStatistics.php
     * Used by all statistics reports for instructor search functionality
     */
    public function getFilteredInstructors(?string $search): Collection
    {
        $query = Instructor::query();

        if (!empty($search)) {
            $search = strtolower($search);

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

    /**
     * Get filtered departments by search query
     *
     * Extracted from Dashboard.php, DetailedExport.php, and SummaryStatistics.php
     * Used by all statistics reports for department search functionality
     */
    public function getFilteredDepartments(?string $search): array
    {
        $allDepartments = $this->departmentService->getAllDepartments();

        if (empty($search)) {
            return $allDepartments;
        }

        $search = strtolower($search);
        $filtered = [];

        foreach ($allDepartments as $code => $name) {
            if (str_contains(strtolower($name), $search) || str_contains(strtolower($code), $search)) {
                $filtered[$code] = $name;
            }
        }

        return $filtered;
    }

    /**
     * Get filtered classes by search query
     *
     * Extracted from Dashboard.php, DetailedExport.php, and SummaryStatistics.php
     * Used by all statistics reports for class search functionality
     */
    public function getFilteredClasses(?string $search): Collection
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

        if (empty($search)) {
            return $classes->take(100);
        }

        $search = strtolower($search);
        return $classes->filter(function($class) use ($search) {
            return str_contains(strtolower($class['display']), $search) ||
                   str_contains(strtolower($class['code']), $search);
        })->take(100);
    }
}
