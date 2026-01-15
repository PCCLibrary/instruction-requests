<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class AcademicTerm extends Model
{
    protected $fillable = [
        'academic_year',
        'term_name',
        'start_date',
        'end_date',
        'sort_order',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'sort_order' => 'integer',
    ];

    /**
     * Get all terms for a specific academic year, ordered by sort_order.
     */
    public static function forYear(string $academicYear): Collection
    {
        return static::where('academic_year', $academicYear)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Get distinct academic years, newest first.
     */
    public static function distinctYears()
    {
        return static::select('academic_year')
            ->distinct()
            ->orderBy('academic_year', 'desc')
            ->pluck('academic_year');
    }

    /**
     * Get term by date lookup.
     */
    public static function findByDate(Carbon $date): ?self
    {
        return static::whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->first();
    }

    /**
     * Get the correct display year for this term.
     * Fall terms use the first year, all other terms use the second year.
     *
     * Example: For academic year "2024-2025":
     * - Fall 2024
     * - Winter 2025
     * - Spring 2025
     * - Summer 2025
     */
    public function getDisplayYearAttribute(): string
    {
        $years = explode('-', $this->academic_year);

        // Fall term uses first year, all others use second year
        return $this->term_name === 'fall' ? $years[0] : $years[1];
    }

    /**
     * Get the full formatted name (e.g., "Fall 2024", "Winter 2025").
     */
    public function getFullNameAttribute(): string
    {
        return ucfirst($this->term_name) . ' ' . $this->display_year;
    }
}
