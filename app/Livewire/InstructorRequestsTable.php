<?php

namespace App\Livewire;

use App\Models\InstructionRequests;
use App\Services\InstructionRequestService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;
use Illuminate\Support\Facades\Auth;

final class InstructorRequestsTable extends PowerGridComponent
{
    use WithExport;

    // Instructor ID from the parent page
    public $instructorId;

    // PowerGrid configuration
    public string $tableName = 'instructor_requests';
    public string $primaryKey = 'instruction_requests.id';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
    public bool $withSortStringNumber = true;

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

            (new Exportable('instructor_requests_' . now()->format('Y-m-d')))
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
        // Use service to get requests by instructor
        $instructionRequestService = app(InstructionRequestService::class);
        $requests = $instructionRequestService->getRequestsByInstructor($this->instructorId);

        return InstructionRequests::query()
            ->whereIn('instruction_requests.id', $requests->pluck('id'))
            ->leftJoin('instruction_request_details', 'instruction_requests.id', '=', 'instruction_request_details.instruction_requests_id')
            ->leftJoin('campuses', 'instruction_requests.campus_id', '=', 'campuses.id')
            ->leftJoin('classes', 'instruction_requests.class_id', '=', 'classes.id')
            ->leftJoin('users as librarians', 'instruction_request_details.assigned_librarian_id', '=', 'librarians.id')
            ->leftJoin('users as lockers', 'instruction_requests.locked_by', '=', 'lockers.id')
            ->select([
                'instruction_requests.*',
                'instruction_request_details.instruction_datetime',
                'campuses.name as campus_name',
                'classes.course_name',
                'librarians.display_name as librarian_name',
                'lockers.display_name as locker_name'
            ]);
    }

    /**
     * Define which fields can be searched in related tables
     */
    public function relationSearch(): array
    {
        return [
            'campus' => ['name'],
            'librarian' => ['display_name'],
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
            ->add('created_at')
            ->add('created_at_formatted', fn (InstructionRequests $model) =>
                Carbon::parse($model->created_at)->format('m/d/y g:i A'))
            ->add('instruction_type')
            ->add('campus_name')
            ->add('course_name')
            ->add('librarian_name')
            ->add('status', fn (InstructionRequests $model) => ucfirst($model->status))
            ->add('instruction_datetime')
            ->add('instruction_datetime_formatted', fn (InstructionRequests $model) =>
                $model->instruction_datetime ? Carbon::parse($model->instruction_datetime)->format('m/d/y g:i A') : '');
    }

    /**
     * Define the table columns configuration
     */
    public function columns(): array
    {
        return [
            Column::make('Received', 'created_at_formatted', 'created_at')
                ->sortable()
                ->searchable()
                ->visibleInExport(false),

            Column::make('Class', 'course_name')
                ->sortable()
                ->searchable()
                ->visibleInExport(true),

            Column::make('Campus', 'campus_name')
                ->sortable()
                ->searchable()
                ->visibleInExport(true),

            Column::make('Librarian', 'librarian_name')
                ->sortable()
                ->searchable()
                ->visibleInExport(true),

            Column::make('Type', 'instruction_type')
                ->sortable()
                ->visibleInExport(true),

            Column::make('Instruction Date', 'instruction_datetime_formatted', 'instruction_datetime')
                ->sortable()
                ->visibleInExport(false),

            Column::action('Action')
                ->visibleInExport(false)
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
