<?php

namespace App\Livewire\Statistics;

use App\Models\InstructionRequests;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;

final class DetailedExportTable extends PowerGridComponent
{
    use WithExport;

    public string $tableName = 'detailed_export_table';
    public string $primaryKey = 'instruction_requests.id';
    public string $sortField = 'instruction_requests.created_at';
    public string $sortDirection = 'desc';
    public bool $withSortStringNumber = true;

    // Filter properties passed from parent
    public $startDate;
    public $endDate;
    public $campus = null;
    public $department = null;
    public $class = null;
    public $instructor = null;
    public $assignedLibrarian = null;

    /**
     * Configure PowerGrid theme
     */
    public function mount(): void
    {
        // Set purple header color to match filter panel
        $themeClass = $this->customThemeClass() ?? strval(config('livewire-powergrid.theme'));
        $theme = new $themeClass();
        $theme->header_color = 'bg-purple-500 dark:bg-purple-600';
        app()->instance($themeClass, $theme);

        parent::mount();
    }

    /**
     * Configure the table setup including export functionality
     */
    public function setUp(): array
    {
        return [
            PowerGrid::header(),

            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),

            (new Exportable('instruction_sessions_' . now()->format('Y-m-d')))
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV)
                ->striped()
                ->columnWidth([
                    1 => 10,
                ])
        ];
    }

    /**
     * Define the data source query with all necessary joins
     */
    public function datasource(): Builder
    {
        $query = InstructionRequests::query()
            ->leftJoin('instructors', 'instruction_requests.instructor_id', '=', 'instructors.id')
            ->leftJoin('classes', 'instruction_requests.class_id', '=', 'classes.id')
            ->leftJoin('instruction_request_details', 'instruction_requests.id', '=', 'instruction_request_details.instruction_requests_id')
            ->leftJoin('users as librarians', 'instruction_request_details.assigned_librarian_id', '=', 'librarians.id')
            ->leftJoin('campuses', 'instruction_requests.campus_id', '=', 'campuses.id')
            ->select([
                'instruction_requests.id',
                'instruction_requests.status',
                'instruction_requests.instruction_type',
                'instruction_requests.created_at',
                'instruction_requests.department',
                'instruction_requests.course_number',
                'campuses.name as campus_name',
                'instructors.display_name as instructor_name',
                'instructors.email as instructor_email',
                'librarians.display_name as librarian_name',
                'classes.course_name',
                'instruction_request_details.instruction_datetime',
            ]);

        // Apply date range filter
        if ($this->startDate && $this->endDate) {
            $query->whereBetween('instruction_requests.created_at', [
                $this->startDate,
                $this->endDate
            ]);
        }

        // Apply secondary filters
        if ($this->campus) {
            $query->where('instruction_requests.campus_id', $this->campus);
        }

        if ($this->department) {
            $query->where('instruction_requests.department', $this->department);
        }

        if ($this->class) {
            // Class format is "DEPT-NUM" so split and match both parts
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

        return $query;
    }

    /**
     * Define which fields can be searched in related tables
     */
    public function relationSearch(): array
    {
        return [
            'instructor' => ['display_name', 'email'],
            'campus' => ['name'],
            'librarian' => ['display_name'],
        ];
    }

    /**
     * Define all fields that will be used in the table and exports
     */
    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('status', fn (InstructionRequests $model) => ucfirst($model->status))
            ->add('instruction_type')
            ->add('campus_name')
            ->add('department')
            ->add('course_number')
            ->add('instructor_name')
            ->add('instructor_email')
            ->add('librarian_name')
            ->add('course_name')
            ->add('created_at_formatted', fn (InstructionRequests $model) =>
                Carbon::parse($model->created_at)->format('m/d/y g:i A'))
            ->add('instruction_datetime_formatted', fn (InstructionRequests $model) =>
                $model->instruction_datetime ? Carbon::parse($model->instruction_datetime)->format('m/d/y g:i A') : 'Not scheduled');
    }

    /**
     * Define the table columns configuration
     * Start with basic columns - will expand to 44 total
     */
    public function columns(): array
    {
        return [
            Column::make('ID', 'id', 'instruction_requests.id')
                ->sortable(),

            Column::make('Status', 'status', 'instruction_requests.status')
                ->sortable(),

            Column::make('Instruction Type', 'instruction_type', 'instruction_requests.instruction_type')
                ->sortable(),

            Column::make('Campus', 'campus_name', 'campuses.name')
                ->sortable()
                ->searchable(),

            Column::make('Department', 'department', 'instruction_requests.department')
                ->sortable()
                ->searchable(),

            Column::make('Course Number', 'course_number', 'instruction_requests.course_number')
                ->sortable()
                ->searchable(),

            Column::make('Instructor Name', 'instructor_name', 'instructors.display_name')
                ->sortable()
                ->searchable(),

            Column::make('Instructor Email', 'instructor_email', 'instructors.email')
                ->sortable()
                ->searchable(),

            Column::make('Assigned Librarian', 'librarian_name', 'librarians.display_name')
                ->sortable()
                ->searchable(),

            Column::make('Created', 'created_at_formatted', 'instruction_requests.created_at')
                ->sortable(),

            Column::make('Instruction Date', 'instruction_datetime_formatted', 'instruction_request_details.instruction_datetime')
                ->sortable(),
        ];
    }

    /**
     * Listen for filter updates from parent component
     */
    #[\Livewire\Attributes\On('applyFilters')]
    public function applyFilters($filters): void
    {
        $this->startDate = $filters['startDate'] ?? null;
        $this->endDate = $filters['endDate'] ?? null;
        $this->campus = $filters['campus'] ?? null;
        $this->department = $filters['department'] ?? null;
        $this->class = $filters['class'] ?? null;
        $this->instructor = $filters['instructor'] ?? null;
        $this->assignedLibrarian = $filters['assignedLibrarian'] ?? null;

        $this->dispatch('pg:eventRefresh-' . $this->tableName);
    }

    /**
     * Define event listeners
     */
    protected function getListeners()
    {
        return array_merge(
            parent::getListeners(),
            [
                'applyFilters',
            ]
        );
    }
}
