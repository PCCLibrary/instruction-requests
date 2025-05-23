<?php

namespace App\Livewire;

use App\Models\InstructionRequests;
use App\Services\InstructionRequestService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use App\Models\Campus;
use Illuminate\Support\Facades\Auth;

final class LibrarianRequestsTable extends PowerGridComponent
{
    // Librarian ID from the parent page
    public $librarianId;

    // PowerGrid configuration
    public string $tableName = 'librarian_requests';
    public string $primaryKey = 'instruction_requests.id';
    public string $sortField = 'instruction_datetime';
    public string $sortDirection = 'asc';
    public bool $withSortStringNumber = true;

    /**
     * Configure the table setup
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
     * Define the data source query with all necessary joins
     */
    public function datasource(): Builder
    {
        // Use service to get requests by librarian
        $instructionRequestService = app(InstructionRequestService::class);
        $requests = $instructionRequestService->getRequestsByLibrarian($this->librarianId);
        $requestIds = $requests->pluck('id')->toArray();

        $query = InstructionRequests::query()
            // Use whereIntegerInRaw instead of whereIn to ensure SQL safety with integers
            ->whereIntegerInRaw('instruction_requests.id', $requestIds)
            ->leftJoin('instruction_request_details', 'instruction_requests.id', '=', 'instruction_request_details.instruction_requests_id')
            ->leftJoin('campuses', 'instruction_requests.campus_id', '=', 'campuses.id')
            ->leftJoin('classes', 'instruction_requests.class_id', '=', 'classes.id')
            ->leftJoin('instructors', 'instruction_requests.instructor_id', '=', 'instructors.id')
            ->leftJoin('users as lockers', 'instruction_requests.locked_by', '=', 'lockers.id')
            ->select([
                'instruction_requests.id',
                'instruction_requests.created_at',
                'instruction_requests.instruction_type',
                'instruction_requests.status',
                'instruction_request_details.instruction_datetime',
                'campuses.name as campus_name',
                'classes.course_name',
                'instructors.display_name as instructor_display_name',
                'instructors.name as instructor_name',
                'lockers.display_name as locker_name',
                'instruction_requests.locked',
                'instruction_requests.locked_by',
                'instruction_requests.locked_at'
            ]);

        return $query;
    }

    /**
     * Define which fields can be searched in related tables
     */
    public function relationSearch(): array
    {
        return [
            'campus' => ['name'],
            'instructor' => ['display_name', 'name'],
            'classes' => ['course_name'],
        ];
    }

    /**
     * Define all fields that will be used in the table and exports
     */
    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('instruction_type')
            ->add('campus_name')
            ->add('course_name')
            ->add('instructor_name', fn (InstructionRequests $model) =>
                $model->instructor_display_name ?: $model->instructor_name)
            ->add('status', fn (InstructionRequests $model) => ucfirst($model->status))
            ->add('instruction_datetime')
            ->add('instruction_datetime_formatted', fn (InstructionRequests $model) =>
                $model->instruction_datetime ? Carbon::parse($model->instruction_datetime)->format('m/d/y g:i A') : 'Not scheduled');
    }

    /**
     * Define the table columns configuration
     */
    public function columns(): array
    {
        return [
            Column::make('Instruction Date', 'instruction_datetime_formatted', 'instruction_request_details.instruction_datetime')
                ->sortable(),

            Column::make('Class', 'course_name', 'classes.course_name')
                ->sortable()
                ->searchable(),

            Column::make('Campus', 'campus_name', 'campuses.name')
                ->sortable()
                ->searchable(),

            Column::make('Instructor', 'instructor_name', 'instructors.display_name')
                ->sortable()
                ->searchable(),

            Column::make('Type', 'instruction_type', 'instruction_requests.instruction_type')
                ->sortable(),

            Column::make('Status', 'status', 'instruction_requests.status')
                ->sortable(),

            Column::action('Action')
        ];
    }

    /**
     * Define the available filters for the table
     */
    public function filters(): array
    {
        // Fetch campuses using ordered scope, selecting 'code' for the value and 'name' for the label
        $campuses = Campus::ordered()
            ->select('code as value', 'name as label')
            ->get()
            ->toArray();

        return [
            // Date range filter for instruction date (following created_at pattern)
            Filter::datepicker('instruction_datetime_formatted', 'instruction_request_details.instruction_datetime'),

            // Instructor text search (like the main instruction request table)
            Filter::inputText('instructor_name', 'instructors.display_name')
                ->operators(['contains']),

            // Class text search (like instructor filter)
            Filter::inputText('course_name', 'classes.course_name')
                ->operators(['contains']),

            // Campus select using ordered scope
            Filter::select('campus_name', 'campuses.code')
                ->dataSource($campuses)
                ->optionValue('value')
                ->optionLabel('label'),

            // Type select (copy from main instruction request table)
            Filter::select('instruction_type', 'instruction_requests.instruction_type')
                ->dataSource([
                    ['value' => 'on-campus', 'label' => 'On Campus'],
                    ['value' => 'remote', 'label' => 'Remote'],
                    ['value' => 'asynchronous', 'label' => 'Asynchronous'],
                ])
                ->optionValue('value')
                ->optionLabel('label'),

            // Status select (copy from main instruction request table)
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
        ];
    }

    /**
     * Define the action buttons for each row using the shared table-actions component
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
            'lockerName' => $lockerName
        ]);
    }

    /**
     * Handle delete confirmation
     */
    #[\Livewire\Attributes\On('confirmDelete')]
    public function confirmDelete($id): void
    {
        $this->js('confirm("Are you sure you want to delete this request?") && $wire.delete(' . $id . ')');
    }

    /**
     * Handle delete action
     */
    #[\Livewire\Attributes\On('delete')]
    public function delete($id): void
    {
        // Fetch the record to check lock status - explicitly use the model to avoid ambiguity
        $instructionRequest = InstructionRequests::query()
            ->where('instruction_requests.id', $id)
            ->first();

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
        if ($instructionRequest) {
            $instructionRequest->delete();
        }

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
                'refreshLockStatus',
            ]
        );
    }
}
