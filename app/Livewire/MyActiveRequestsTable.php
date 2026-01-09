<?php

namespace App\Livewire;

use App\Models\InstructionRequests;
use App\Services\InstructionRequestService;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Masmerise\Toaster\Toaster;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class MyActiveRequestsTable extends PowerGridComponent
{
    use WithExport;

    public string $tableName = 'MyActiveRequestsTable';

    public function mount(): void
    {
        $themeClass = $this->customThemeClass() ?? strval(config('livewire-powergrid.theme'));

        $theme = new $themeClass();
        $theme->header_color = 'bg-teal-500 dark:bg-teal-600';

        app()->instance($themeClass, $theme);

        parent::mount();
    }

    public function setUp(): array
    {
        return [
            PowerGrid::header(),

            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

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
            ])
            ->whereIn('instruction_requests.status', ['accepted', 'in_progress'])
            ->where('instruction_request_details.assigned_librarian_id', Auth::id())
            ->orderBy('instruction_request_details.instruction_datetime', 'asc');
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('campus_name')
            ->add('course_name')
            ->add('instructor_name')
            ->add('instruction_type')
            ->add('instruction_datetime')
            ->add('instruction_datetime_formatted', fn ($request) =>
                $request->instruction_datetime
                    ? Carbon::parse($request->instruction_datetime)->format('m/d/Y g:i A')
                    : 'N/A')
            ->add('status')
            ->add('status_formatted', fn ($request) => ucwords(str_replace('_', ' ', $request->status)));
    }

    public function columns(): array
    {
        return [
            Column::make('Instruction Date', 'instruction_datetime_formatted', 'instruction_datetime')
                ->sortable()
                ->searchable(),

            Column::make('Instructor', 'instructor_name')
                ->sortable()
                ->searchable(),

            Column::make('Type', 'instruction_type')
                ->sortable()
                ->searchable(),

            Column::make('Campus', 'campus_name')
                ->sortable()
                ->searchable(),

            Column::make('Class', 'course_name')
                ->sortable()
                ->searchable(),

            Column::make('Status', 'status_formatted', 'status')
                ->sortable()
                ->searchable(),

            Column::action('Actions')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::select('status', 'instruction_requests.status')
                ->dataSource([
                    ['id' => 'accepted', 'name' => 'Accepted'],
                    ['id' => 'in_progress', 'name' => 'In Progress'],
                ])
                ->optionValue('id')
                ->optionLabel('name'),

            Filter::select('instruction_type', 'instruction_requests.instruction_type')
                ->dataSource([
                    ['id' => 'on-campus', 'name' => 'On-Campus'],
                    ['id' => 'remote', 'name' => 'Remote'],
                    ['id' => 'asynchronous', 'name' => 'Asynchronous'],
                ])
                ->optionValue('id')
                ->optionLabel('name'),

            Filter::datetimepicker('instruction_datetime', 'instruction_request_details.instruction_datetime'),
        ];
    }

    public function actionsFromView($row): \Illuminate\View\View
    {
        $isLocked = (bool)$row->locked;
        $lockerName = $row->locker_name;

        return view('components.my-active-requests-actions', [
            'id' => $row->id,
            'status' => $row->status,
            'instructionType' => $row->instruction_type,
            'editRoute' => 'instructionRequests.edit',
            'routeKeyName' => 'instructionRequest',
            'size' => 'w-4 h-4',
            'isLocked' => $isLocked,
            'lockerName' => $lockerName,
        ]);
    }

    public function markInProgress($id)
    {
        try {
            $instructionRequestService = app(InstructionRequestService::class);
            $instructionRequestService->updateInstructionRequest([
                'status' => 'in_progress'
            ], $id);

            app(Toaster::class)->success('Request marked as in progress.');
            $this->dispatch('pg:eventRefresh-' . $this->tableName);
        } catch (\Exception $e) {
            Log::error('Failed to mark request as in progress', [
                'request_id' => $id,
                'error' => $e->getMessage()
            ]);
            app(Toaster::class)->error('Failed to update request status.');
        }
    }

    public function markComplete($id)
    {
        try {
            $instructionRequestService = app(InstructionRequestService::class);
            $instructionRequestService->updateInstructionRequest([
                'status' => 'completed'
            ], $id);

            app(Toaster::class)->success('Request marked as completed.');
            $this->dispatch('pg:eventRefresh-' . $this->tableName);
        } catch (\Exception $e) {
            Log::error('Failed to mark request as completed', [
                'request_id' => $id,
                'error' => $e->getMessage()
            ]);
            app(Toaster::class)->error('Failed to update request status.');
        }
    }

    public function openScheduleModal(int $id): void
    {
        Log::info('OpenScheduleModal dispatching event', [
            'id' => $id
        ]);

        // Dispatch event to parent dashboard
        $this->dispatch('open-schedule-modal', id: $id);
    }
}
