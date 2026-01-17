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

    // Column visibility - track which columns to display
    public array $visibleColumns = [];

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

        // Initialize with all columns visible by default ONLY if parent didn't pass visible columns
        if (empty($this->visibleColumns)) {
            $this->visibleColumns = [
                'id', 'status', 'instruction_type', 'number_of_students', 'campus_name',
                'department', 'course_number', 'course_crn', 'instructor_name',
                'instructor_email', 'requested_librarian_name', 'librarian_name',
                'room', 'genai_discussion_interest', 'ada_provisions_needed',
                'ada_provisions_description', 'preferred_datetime', 'alternate_datetime',
                'duration', 'asynchronous_instruction_ready_date', 'instruction_datetime_formatted',
                'instruction_duration', 'video', 'non_video', 'modified_tutorial',
                'embedded', 'research_guide', 'handout', 'developed_assignment',
                'other_materials', 'other_describe', 'created_at_formatted'
            ];
        }

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
            ->leftJoin('users as requested_librarians', 'instruction_requests.librarian_id', '=', 'requested_librarians.id')
            ->leftJoin('campuses', 'instruction_requests.campus_id', '=', 'campuses.id')
            ->select([
                'instruction_requests.id',
                'instruction_requests.status',
                'instruction_requests.instruction_type',
                'instruction_requests.number_of_students',
                'instruction_requests.created_at',
                'instruction_requests.department',
                'instruction_requests.course_number',
                'instruction_requests.course_crn',
                'instruction_requests.genai_discussion_interest',
                'instruction_requests.ada_provisions_needed',
                'instruction_requests.ada_provisions_description',
                'instruction_requests.preferred_datetime',
                'instruction_requests.alternate_datetime',
                'instruction_requests.duration',
                'instruction_requests.asynchronous_instruction_ready_date',
                'campuses.name as campus_name',
                'instructors.display_name as instructor_name',
                'instructors.email as instructor_email',
                'requested_librarians.display_name as requested_librarian_name',
                'librarians.display_name as librarian_name',
                'classes.course_name',
                'instruction_request_details.instruction_datetime',
                'instruction_request_details.instruction_duration',
                'instruction_request_details.room',
                'instruction_request_details.video',
                'instruction_request_details.non_video',
                'instruction_request_details.modified_tutorial',
                'instruction_request_details.embedded',
                'instruction_request_details.research_guide',
                'instruction_request_details.handout',
                'instruction_request_details.developed_assignment',
                'instruction_request_details.other_materials',
                'instruction_request_details.other_describe',
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
            ->add('number_of_students')
            ->add('campus_name')
            ->add('department')
            ->add('course_number')
            ->add('course_crn')
            ->add('instructor_name')
            ->add('instructor_email')
            ->add('requested_librarian_name')
            ->add('librarian_name')
            ->add('room')
            ->add('genai_discussion_interest')
            ->add('ada_provisions_needed', fn (InstructionRequests $model) =>
                $model->ada_provisions_needed ? 'Yes' : 'No')
            ->add('ada_provisions_description')
            ->add('preferred_datetime', fn (InstructionRequests $model) =>
                $model->preferred_datetime ? Carbon::parse($model->preferred_datetime)->format('m/d/y g:i A') : '')
            ->add('alternate_datetime', fn (InstructionRequests $model) =>
                $model->alternate_datetime ? Carbon::parse($model->alternate_datetime)->format('m/d/y g:i A') : '')
            ->add('duration')
            ->add('asynchronous_instruction_ready_date', fn (InstructionRequests $model) =>
                $model->asynchronous_instruction_ready_date ? Carbon::parse($model->asynchronous_instruction_ready_date)->format('m/d/y') : '')
            ->add('instruction_duration')
            ->add('video', fn (InstructionRequests $model) =>
                $model->video ? 'Yes' : 'No')
            ->add('non_video', fn (InstructionRequests $model) =>
                $model->non_video ? 'Yes' : 'No')
            ->add('modified_tutorial', fn (InstructionRequests $model) =>
                $model->modified_tutorial ? 'Yes' : 'No')
            ->add('embedded', fn (InstructionRequests $model) =>
                $model->embedded ? 'Yes' : 'No')
            ->add('research_guide', fn (InstructionRequests $model) =>
                $model->research_guide ? 'Yes' : 'No')
            ->add('handout', fn (InstructionRequests $model) =>
                $model->handout ? 'Yes' : 'No')
            ->add('developed_assignment', fn (InstructionRequests $model) =>
                $model->developed_assignment ? 'Yes' : 'No')
            ->add('other_materials', fn (InstructionRequests $model) =>
                $model->other_materials ? 'Yes' : 'No')
            ->add('other_describe')
            ->add('course_name')
            ->add('created_at_formatted', fn (InstructionRequests $model) =>
                Carbon::parse($model->created_at)->format('m/d/y g:i A'))
            ->add('instruction_datetime_formatted', fn (InstructionRequests $model) =>
                $model->instruction_datetime ? Carbon::parse($model->instruction_datetime)->format('m/d/y g:i A') : 'Not scheduled');
    }

    /**
     * Define the table columns configuration
     * Columns are conditionally built based on $visibleColumns array
     * This method is called on mount, so wire:key re-mount triggers rebuild
     */
    public function columns(): array
    {
        $columns = [];

        // Conditionally add each column only if it's in visibleColumns array
        if (in_array('id', $this->visibleColumns)) {
            $columns[] = Column::make('ID', 'id', 'instruction_requests.id')
                ->sortable();
        }

        if (in_array('status', $this->visibleColumns)) {
            $columns[] = Column::make('Status', 'status', 'instruction_requests.status')
                ->sortable();
        }

        if (in_array('instruction_type', $this->visibleColumns)) {
            $columns[] = Column::make('Instruction Type', 'instruction_type', 'instruction_requests.instruction_type')
                ->sortable();
        }

        if (in_array('number_of_students', $this->visibleColumns)) {
            $columns[] = Column::make('Number of Students', 'number_of_students', 'instruction_requests.number_of_students')
                ->sortable();
        }

        if (in_array('campus_name', $this->visibleColumns)) {
            $columns[] = Column::make('Campus', 'campus_name', 'campuses.name')
                ->sortable()
                ->searchable();
        }

        if (in_array('department', $this->visibleColumns)) {
            $columns[] = Column::make('Department', 'department', 'instruction_requests.department')
                ->sortable()
                ->searchable();
        }

        if (in_array('course_number', $this->visibleColumns)) {
            $columns[] = Column::make('Course Number', 'course_number', 'instruction_requests.course_number')
                ->sortable()
                ->searchable();
        }

        if (in_array('course_crn', $this->visibleColumns)) {
            $columns[] = Column::make('Course CRN', 'course_crn', 'instruction_requests.course_crn')
                ->sortable()
                ->searchable();
        }

        if (in_array('instructor_name', $this->visibleColumns)) {
            $columns[] = Column::make('Instructor Name', 'instructor_name', 'instructors.display_name')
                ->sortable()
                ->searchable();
        }

        if (in_array('instructor_email', $this->visibleColumns)) {
            $columns[] = Column::make('Instructor Email', 'instructor_email', 'instructors.email')
                ->sortable()
                ->searchable();
        }

        if (in_array('requested_librarian_name', $this->visibleColumns)) {
            $columns[] = Column::make('Requested Librarian', 'requested_librarian_name', 'requested_librarians.display_name')
                ->sortable()
                ->searchable();
        }

        if (in_array('librarian_name', $this->visibleColumns)) {
            $columns[] = Column::make('Assigned Librarian', 'librarian_name', 'librarians.display_name')
                ->sortable()
                ->searchable();
        }

        if (in_array('room', $this->visibleColumns)) {
            $columns[] = Column::make('Room', 'room', 'instruction_request_details.room')
                ->sortable()
                ->searchable();
        }

        if (in_array('genai_discussion_interest', $this->visibleColumns)) {
            $columns[] = Column::make('GenAI Discussion Interest', 'genai_discussion_interest', 'instruction_requests.genai_discussion_interest')
                ->sortable()
                ->searchable();
        }

        if (in_array('ada_provisions_needed', $this->visibleColumns)) {
            $columns[] = Column::make('ADA Provisions Needed', 'ada_provisions_needed')
                ->sortable();
        }

        if (in_array('ada_provisions_description', $this->visibleColumns)) {
            $columns[] = Column::make('ADA Accommodations', 'ada_provisions_description', 'instruction_requests.ada_provisions_description')
                ->sortable()
                ->searchable();
        }

        if (in_array('preferred_datetime', $this->visibleColumns)) {
            $columns[] = Column::make('Preferred Date & Time', 'preferred_datetime', 'instruction_requests.preferred_datetime')
                ->sortable();
        }

        if (in_array('alternate_datetime', $this->visibleColumns)) {
            $columns[] = Column::make('Alternate Date & Time', 'alternate_datetime', 'instruction_requests.alternate_datetime')
                ->sortable();
        }

        if (in_array('duration', $this->visibleColumns)) {
            $columns[] = Column::make('Duration', 'duration', 'instruction_requests.duration')
                ->sortable()
                ->searchable();
        }

        if (in_array('asynchronous_instruction_ready_date', $this->visibleColumns)) {
            $columns[] = Column::make('Async Ready Date', 'asynchronous_instruction_ready_date', 'instruction_requests.asynchronous_instruction_ready_date')
                ->sortable();
        }

        if (in_array('instruction_duration', $this->visibleColumns)) {
            $columns[] = Column::make('Instruction Duration', 'instruction_duration', 'instruction_request_details.instruction_duration')
                ->sortable()
                ->searchable();
        }

        if (in_array('video', $this->visibleColumns)) {
            $columns[] = Column::make('Video', 'video')
                ->sortable();
        }

        if (in_array('non_video', $this->visibleColumns)) {
            $columns[] = Column::make('Non-Video', 'non_video')
                ->sortable();
        }

        if (in_array('modified_tutorial', $this->visibleColumns)) {
            $columns[] = Column::make('Modified Tutorial', 'modified_tutorial')
                ->sortable();
        }

        if (in_array('embedded', $this->visibleColumns)) {
            $columns[] = Column::make('Embedded', 'embedded')
                ->sortable();
        }

        if (in_array('research_guide', $this->visibleColumns)) {
            $columns[] = Column::make('Research Guide', 'research_guide')
                ->sortable();
        }

        if (in_array('handout', $this->visibleColumns)) {
            $columns[] = Column::make('Handout', 'handout')
                ->sortable();
        }

        if (in_array('developed_assignment', $this->visibleColumns)) {
            $columns[] = Column::make('Developed Assignment', 'developed_assignment')
                ->sortable();
        }

        if (in_array('other_materials', $this->visibleColumns)) {
            $columns[] = Column::make('Other Materials', 'other_materials')
                ->sortable();
        }

        if (in_array('other_describe', $this->visibleColumns)) {
            $columns[] = Column::make('Other Materials Description', 'other_describe', 'instruction_request_details.other_describe')
                ->sortable()
                ->searchable();
        }

        if (in_array('created_at_formatted', $this->visibleColumns)) {
            $columns[] = Column::make('Created', 'created_at_formatted', 'instruction_requests.created_at')
                ->sortable();
        }

        if (in_array('instruction_datetime_formatted', $this->visibleColumns)) {
            $columns[] = Column::make('Instruction Date', 'instruction_datetime_formatted', 'instruction_request_details.instruction_datetime')
                ->sortable();
        }

        return $columns;
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
