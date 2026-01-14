<?php

namespace App\Services;

use Carbon\Carbon;

class TermService
{
    /**
     * Create a new TermService instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Calculate next Monday after a given date.
     */
    public function getNextMonday(Carbon $date): Carbon
    {
        $next = $date->copy()->addDay();

        while ($next->dayOfWeek !== Carbon::MONDAY) {
            $next->addDay();
        }

        return $next;
    }

    /**
     * Get first Monday after New Year's Day for a given year.
     */
    public function getFirstMondayAfterNewYear(int $year): Carbon
    {
        $newYear = Carbon::create($year, 1, 1);
        return $this->getNextMonday($newYear);
    }

    /**
     * Calculate current academic year (Sept-Aug cycle).
     */
    public function getCurrentAcademicYear(): string
    {
        $now = now();

        if ($now->month >= 9) {
            // Sept-Dec: current year to next year
            return $now->year . '-' . ($now->year + 1);
        }

        // Jan-Aug: previous year to current year
        return ($now->year - 1) . '-' . $now->year;
    }

    /**
     * Get available fiscal years (for dropdown population).
     */
    public function getAvailableFiscalYears(): array
    {
        $currentYear = now()->year;
        $years = [];

        // Generate 5 years: 2 past, current, 2 future
        for ($i = -2; $i <= 2; $i++) {
            $year = $currentYear + $i;
            $years[$year] = "FY {$year}-" . ($year + 1);
        }

        return $years;
    }
}
