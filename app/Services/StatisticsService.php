<?php

namespace App\Services;

use App\Models\InstructionRequests;
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

    public function __construct(DepartmentService $departmentService)
    {
        $this->departmentService = $departmentService;
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
     */
    public function getSummaryMetrics(array $columns, array $filters = []): array
    {
        $totalMinutes = 0;
        $totalStudents = 0;
        $totalSessions = 0;
        $adaSessions = 0;
        $scheduledAndCompleted = 0;
        $completedOnly = 0;

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
        for ($ay = $maxAY; $ay >= $minAY; $ay--) {
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
}
