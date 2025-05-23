<?php

namespace App\Livewire;

use App\Models\InstructionRequests;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Responsive;
use App\Models\Campus;

use Illuminate\Support\Facades\Auth;

final class InstructionRequestTable extends PowerGridComponent
{
    use WithExport;

    // Define table properties
    public string $tableName = 'instruction_requests.instruction_requests';
    public string $primaryKey = 'instruction_requests.id';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
    public bool $withSortStringNumber = true;

    /**
     * Configure the table setup including export functionality
     */
    public function setUp(): array
    {
        //$this->showCheckBox();

        return [


            PowerGrid::header(),

            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),

            (new Exportable('instruction_requests_' . now()->format('Y-m-d')))
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV)
                ->stripTags(true)
                ->csvDelimiter('"')
                ->csvSeparator(','),
        ];
    }

    /**
     * Define the data source query with all necessary joins
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
                'instruction_requests.status',
                'instruction_request_details.instruction_datetime',
                'instruction_request_details.created_by',
                'instruction_request_details.last_updated_by',
                'campuses.name as campus_name',
                'lockers.display_name as locker_name'
            ]);
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
            ->add('created_at')
            ->add('created_at_formatted', fn (InstructionRequests $model) =>
            Carbon::parse($model->created_at)->format('m/d/y g:i A'))
            ->add('instructor_name')
            ->add('instruction_type')
            ->add('librarian_name')
            ->add('campus_name')
            ->add('course_name')
            ->add('status', fn (InstructionRequests $model) => ucfirst($model->status))
            ->add('instruction_datetime')
            ->add('instruction_datetime_formatted', fn (InstructionRequests $model) =>
            $model->instruction_datetime ? Carbon::parse($model->instruction_datetime)->format('m/d/y g:i A') : '')
            ->add('last_updated_by');
    }

    /**
     * Define the table columns configuration
     */
    public function columns(): array
    {
        return [
            Column::make('Created', 'created_at_formatted', 'created_at')
                ->sortable()
                ->searchable()
                ->visibleInExport(false),


            Column::make('Instruction Date', 'instruction_datetime_formatted', 'instruction_datetime')
                ->sortable()
                ->searchable()
                ->visibleInExport(false),


            Column::make('Submitted', 'created_at')
                ->hidden()
                ->visibleInExport(true),

            Column::make('Instructor', 'instructor_name')
                ->sortable()
                ->searchable()
                ->visibleInExport(true),


            Column::make('Type', 'instruction_type')
                ->sortable()
                ->visibleInExport(true),

//                ->searchable(),

            Column::make('Assigned', 'librarian_name')
                ->sortable()
                ->searchable()
                ->visibleInExport(true),


            Column::make('Campus', 'campus_name')
                ->sortable()
                ->searchable()
                ->visibleInExport(true),


            Column::make('Course', 'course_name')
                ->sortable()
                ->visibleInExport(true),

//                ->searchable(),

            Column::make('Status', 'status')
                ->sortable()
                ->visibleInExport(true),

//                ->searchable(),

            Column::action('Action')
                ->visibleInExport(false)
        ];
    }

    /**
     * Define the available filters for the table
     */
    public function filters(): array
    {

        // Fetch campuses, selecting 'code' for the value and 'name' for the label,
        // directly from the database and format as an array.
        $campuses = Campus::ordered()
            ->select('code as value', 'name as label')
            ->get()
            ->toArray();

        return [
            Filter::inputText('instructor_name', 'instructors.display_name')
                ->operators(['contains']),

            Filter::inputText('librarian_name', 'librarians.display_name')
            ->operators(['contains']),

//            Filter::inputText('campus_name', 'campuses.name')
//                ->operators(['contains']),

            Filter::select('campus_name', 'campuses.code')
                ->dataSource($campuses)
                ->optionValue('value')
                ->optionLabel('label'),


            Filter::select('status', 'instruction_requests.status')
                ->dataSource([
                    ['value' => 'received', 'label' => 'Received'],
                    ['value' => 'assigned', 'label' => 'Assigned'],
                    ['value' => 'accepted', 'label' => 'Accepted'],
                    ['value' => 'rejected', 'label' => 'Rejected'],
                    ['value' => 'scheduled', 'label' => 'Scheduled'],
                    ['value' => 'completed', 'label' => 'Completed'],
                ])
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
            Filter::datepicker('instruction_datetime'),
        ];
    }

    /**
     * Define the action buttons for each row
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
     * Handle delete confirmation
     */
    #[\Livewire\Attributes\On('confirmDelete')]
    public function confirmDelete($id): void
    {
        // Directly call delete without showing a second confirmation dialog
        $this->delete($id);
    }

    /**
     * Handle delete action
     */
    #[\Livewire\Attributes\On('delete')]
    public function delete($id): void
    {
        // Fetch the record to check lock status
        $instructionRequest = InstructionRequests::find($id);

        // If record is locked by someone else, show warning and don't delete
        if ($instructionRequest && $instructionRequest->isLocked() && $instructionRequest->locked_by !== auth()->id()) {
            $locker = $instructionRequest->lockedBy;
            $this->dispatch('flash-message', [
                'type' => 'warning',
                'message' => "Cannot delete: This request is currently being edited by {$locker->display_name}."
            ]);
            return;
        }

        // Otherwise proceed with deletion
        InstructionRequests::destroy($id);
        $this->dispatch('pg:eventRefresh-' . $this->tableName);
    }


    #[\Livewire\Attributes\On('filterByLibrarian')]
    public function filterByLibrarian($name): void
    {
        // Clear any existing filters
        $this->filters = [];

        $this->filters['input_text']['librarians.display_name'] = $name;
        $this->dispatch('pg:eventRefresh-' . $this->tableName);
    }

    /**
     * Handle "Expiring Received" filter
     */
    #[\Livewire\Attributes\On('filterExpiringReceived')]
    public function filterExpiringReceived(): void
    {
        // Clear any existing filters
        $this->filters = [];

        // Apply status filter for 'received'
        $this->filters['select']['instruction_requests.status'] = 'received';

        // Apply date range filter for next 14 days
        $startDate = now()->startOfDay();
        $endDate = now()->addDays(14)->endOfDay();

        // Format dates for PowerGrid's datepicker filter
        $this->filters['datepicker']['instruction_datetime'] = [
            'start' => $startDate->format('Y-m-d'),
            'end' => $endDate->format('Y-m-d')
        ];

        // Refresh the table
        $this->dispatch('pg:eventRefresh-' . $this->tableName);
    }

    /**
     * Handle "Rejected" filter
     */
    #[\Livewire\Attributes\On('filterRejected')]
    public function filterRejected(): void
    {
        // Clear any existing filters
        $this->filters = [];

        // Apply status filter for 'rejected'
        $this->filters['select']['instruction_requests.status'] = 'rejected';

        // Refresh the table
        $this->dispatch('pg:eventRefresh-' . $this->tableName);
    }

    #[\Livewire\Attributes\On('filterByCampus')]
    public function filterByCampus($code): void
    {
        // Clear any existing filters
        $this->filters = [];

        $this->filters['select']['campuses.code'] = $code;
        $this->dispatch('pg:eventRefresh-' . $this->tableName);
    }

    #[\Livewire\Attributes\On('filterByStatus')]
    public function filterByStatus($status): void
    {
        // Clear any existing filters
        $this->filters = [];

        $this->filters['select']['instruction_requests.status'] = $status;
        $this->dispatch('pg:eventRefresh-' . $this->tableName);
    }

    #[\Livewire\Attributes\On('clearFilters')]
    public function clearFilters(): void
    {
        $this->filters = [];
        $this->dispatch('pg:eventRefresh-' . $this->tableName);
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
     * Handle session flash message events from JavaScript.
     *
     * @param array $data
     * @return void
     */
    #[\Livewire\Attributes\On('flash-message')]
    public function handleFlashMessage($data): void
    {
        if (isset($data['type']) && isset($data['message'])) {
            session()->flash($data['type'], $data['message']);
        }
    }

    /**
     * Define event listeners
     */
    protected function getListeners()
    {
        return array_merge(
            parent::getListeners(),
            [
                'confirmDelete',
                'delete',
                'instructionRequestUpdated' => '$refresh',
                'filterByLibrarian',
                'filterByCampus',
                'filterByStatus',
                'clearFilters',
                'refreshLockStatus',
                'filterExpiringReceived',  // Add new listener
                'filterRejected'           // Add new listener
            ]
        );
    }
}
