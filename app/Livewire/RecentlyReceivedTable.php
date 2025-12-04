<?php

namespace App\Livewire;

use App\Models\InstructionRequests;
use App\Models\Campus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class RecentlyReceivedTable extends PowerGridComponent
{
    // Define table properties
    public string $tableName = 'recently_received.instruction_requests';
    public string $primaryKey = 'instruction_requests.id';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    /**
     * Set PowerGrid table header color to green (Recently Received)
     */
    public function mount(): void
    {
        $themeClass = $this->customThemeClass() ?? strval(config('livewire-powergrid.theme'));

        $theme = new $themeClass();
        $theme->header_color = 'bg-green-500 dark:bg-green-700';

        app()->instance($themeClass, $theme);

        parent::mount();
    }

    /**
     * Configure the table setup - NO export buttons
     */
    public function setUp(): array
    {
        return [
            PowerGrid::header(),

            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    /**
     * Define the data source query - ALL received requests (no librarian filter)
     */
    public function datasource(): Builder
    {
        return InstructionRequests::query()
            ->leftJoin('instructors', 'instruction_requests.instructor_id', '=', 'instructors.id')
            ->leftJoin('classes', 'instruction_requests.class_id', '=', 'classes.id')
            ->leftJoin('instruction_request_details', 'instruction_requests.id', '=', 'instruction_request_details.instruction_requests_id')
            ->leftJoin('users as librarians', 'instruction_request_details.assigned_librarian_id', '=', 'librarians.id')
            ->leftJoin('campuses', 'instruction_requests.campus_id', '=', 'campuses.id')
            ->leftJoin('users as lockers', 'instruction_requests.locked_by', '=', 'lockers.id')
            ->select([
                'instruction_requests.*',
                'instructors.display_name as instructor_name',
                'instructors.email as instructor_email',
                'classes.course_name',
                'librarians.display_name as librarian_name',
                'instruction_request_details.instruction_datetime',
                'campuses.name as campus_name',
                'lockers.display_name as locker_name'
            ])
            ->where('instruction_requests.status', 'received');
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
     * Define the field mappings
     */
    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('created_at')
            ->add('created_at_formatted', fn($row) => Carbon::parse($row->created_at)->format('m/d/y g:i A'))
            ->add('instructor_name')
            ->add('instruction_type', fn($row) => ucfirst(str_replace('-', ' ', $row->instruction_type)))
            ->add('librarian_name')
            ->add('campus_name')
            ->add('course_name')
            ->add('status', fn($row) => ucfirst($row->status))
            ->add('instruction_datetime')
            ->add('instruction_datetime_formatted', fn($row) =>
                $row->instruction_datetime ? Carbon::parse($row->instruction_datetime)->format('m/d/y g:i A') : ''
            )
            ->add('is_locked', fn($row) => !is_null($row->locked_by))
            ->add('locker_name');
    }

    /**
     * Define the table columns - match InstructionRequestTable
     */
    public function columns(): array
    {
        return [
            Column::make('Created', 'created_at_formatted', 'created_at')
                ->sortable()
                ->searchable(),

            Column::make('Instruction Date', 'instruction_datetime_formatted', 'instruction_request_details.instruction_datetime')
                ->sortable()
                ->searchable(),

            Column::make('Instructor', 'instructor_name')
                ->sortable()
                ->searchable(),

            Column::make('Type', 'instruction_type')
                ->sortable(),

            Column::make('Assigned', 'librarian_name')
                ->sortable()
                ->searchable(),

            Column::make('Campus', 'campus_name')
                ->sortable()
                ->searchable(),

            Column::make('Course', 'course_name')
                ->sortable()
                ->searchable(),

            Column::make('Status', 'status')
                ->sortable(),

            Column::action('Action')
        ];
    }

    /**
     * Define filters - match InstructionRequestTable pattern
     */
    public function filters(): array
    {
        $campuses = Campus::ordered()
            ->select('code as value', 'name as label')
            ->get()
            ->toArray();

        return [
            Filter::inputText('instructor_name', 'instructors.display_name')
                ->operators(['contains']),

            Filter::inputText('librarian_name', 'librarians.display_name')
                ->operators(['contains']),

            Filter::select('campus_name', 'campuses.code')
                ->dataSource($campuses)
                ->optionValue('value')
                ->optionLabel('label'),

            Filter::select('instruction_type', 'instruction_requests.instruction_type')
                ->dataSource([
                    ['value' => 'on-campus', 'label' => 'On Campus'],
                    ['value' => 'remote', 'label' => 'Remote'],
                    ['value' => 'asynchronous', 'label' => 'Asynchronous'],
                ])
                ->optionValue('value')
                ->optionLabel('label'),

            Filter::datepicker('created_at', 'instruction_requests.created_at'),
            Filter::datepicker('instruction_request_details.instruction_datetime', 'instruction_request_details.instruction_datetime'),

            Filter::inputText('course_name', 'classes.course_name')
                ->operators(['contains']),
        ];
    }

    /**
     * Define custom actions using blade component
     */
    public function actionsFromView($row): View
    {
        $isLocked = (bool)$row->locked;
        $lockerName = $row->locker_name;

        return view('components.table-actions', [
            'id' => $row->id,
            'editRoute' => 'instructionRequests.edit',
            'deleteEvent' => 'confirmDelete',
            'canEdit' => !$isLocked,
            'canDelete' => true,
            'size' => 'w-4 h-4',
            'routeKeyName' => 'instructionRequest',
            'isLocked' => $isLocked,
            'lockerName' => $lockerName,
            'confirmMessage' => 'Are you sure you want to delete this request?'
        ]);
    }

    /**
     * Handle refreshing lock status (for polling updates)
     */
    #[\Livewire\Attributes\On('refreshLockStatus')]
    public function refreshLockStatus(): void
    {
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
                'refreshLockStatus',
            ]
        );
    }
}
