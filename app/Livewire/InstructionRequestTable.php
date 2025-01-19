<?php

namespace App\Livewire;

use App\Models\InstructionRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;
use Illuminate\Support\Facades\Auth;

final class InstructionRequestTable extends PowerGridComponent
{
    use WithExport;

    // Define table properties
    public string $tableName = 'instruction_requests';
    public string $primaryKey = 'instruction_requests.id';

    /**
     * Configure the table setup including export functionality
     */
    public function setUp(): array
    {
        $this->showCheckBox();

        return [
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
        return InstructionRequest::query()
            ->leftJoin('instructors', 'instruction_requests.instructor_id', '=', 'instructors.id')
            ->leftJoin('classes', 'instruction_requests.class_id', '=', 'classes.id')
            ->leftJoin('instruction_request_details', 'instruction_requests.id', '=', 'instruction_request_details.instruction_request_id')
            ->leftJoin('users as librarians', 'instruction_request_details.assigned_librarian_id', '=', 'librarians.id')
            ->leftJoin('campuses', 'instruction_requests.campus_id', '=', 'campuses.id')
            ->select([
                'instruction_requests.*',
                'instructors.display_name as instructor_name',
                'instructors.email as instructor_email',
                'classes.course_name',
                'librarians.display_name as librarian_name',
                'instruction_requests.status',
                'instruction_request_details.created_by',
                'instruction_request_details.last_updated_by',
                'campuses.name as campus_name'
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
            ->add('created_at_formatted', fn (InstructionRequest $model) =>
            Carbon::parse($model->created_at)->format('m/d/Y g:i a'))
            ->add('instructor_name')
            ->add('instruction_type')
            ->add('librarian_name')
            ->add('campus_name')
            ->add('course_name')
            ->add('status', fn (InstructionRequest $model) => ucfirst($model->status))
            ->add('preferred_datetime')
            ->add('preferred_datetime_formatted', fn (InstructionRequest $model) =>
            $model->preferred_datetime ? Carbon::parse($model->preferred_datetime)->format('m/d/Y g:i a') : '')
            ->add('last_updated_by');
    }

    /**
     * Define the table columns configuration
     */
    public function columns(): array
    {
        return [
            Column::make('Submitted', 'created_at_formatted', 'created_at')
                ->sortable()
                ->searchable()
                ->visibleInExport(false),

            Column::make('Submitted', 'created_at')
                ->hidden()
                ->visibleInExport(true),

            Column::make('Instructor', 'instructor_name')
                ->sortable()
                ->searchable(),

            Column::make('Type', 'instruction_type')
                ->sortable()
                ->searchable(),

            Column::make('Librarian', 'librarian_name')
                ->sortable()
                ->searchable(),

            Column::make('Campus', 'campus_name')
                ->sortable()
                ->searchable(),

            Column::make('Course', 'course_name')
                ->sortable()
                ->searchable(),

            Column::make('Status', 'status')
                ->sortable()
                ->searchable(),

            Column::make('Preferred Date', 'preferred_datetime_formatted', 'preferred_datetime')
                ->sortable()
                ->searchable()
                ->visibleInExport(false),

            Column::make('Preferred Date', 'preferred_datetime')
                ->hidden()
                ->visibleInExport(true),

            Column::action('Action')
                ->visibleInExport(false),
        ];
    }

    /**
     * Define the available filters for the table
     */
    public function filters(): array
    {
        return [
            Filter::select('status', 'instruction_requests.status')
                ->dataSource([
                    ['value' => 'received', 'label' => 'Received'],
                    ['value' => 'assigned', 'label' => 'Assigned'],
                    ['value' => 'accepted', 'label' => 'Accepted'],
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

            Filter::datepicker('created_at'),

            Filter::datepicker('preferred_datetime'),
        ];
    }

    /**
     * Define the action buttons for each row
     */
    public function actions(InstructionRequest $row): array
    {
        return [
            Button::add('edit')
                ->slot('<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>')
                ->class('inline-flex items-center px-2 py-1 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150')
                ->route('instructionRequests.edit', ['id' => $row->id]),

            Button::add('delete')
                ->slot('<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>')
                ->class('inline-flex items-center px-2 py-1 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150')
                ->dispatch('confirmDelete', ['id' => $row->id])
        ];
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
        InstructionRequest::destroy($id);
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
                'confirmDelete',
                'delete',
                'instructionRequestUpdated' => '$refresh',
            ]
        );
    }
}
