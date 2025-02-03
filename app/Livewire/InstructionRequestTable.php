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
            ->add('created_at_formatted', fn (InstructionRequests $model) =>
            Carbon::parse($model->created_at)->format('m/d/Y g:i a'))
            ->add('instructor_name')
            ->add('instruction_type')
            ->add('librarian_name')
            ->add('campus_name')
            ->add('course_name')
            ->add('status', fn (InstructionRequests $model) => ucfirst($model->status))
            ->add('instruction_datetime')
            ->add('instruction_datetime_formatted', fn (InstructionRequests $model) =>
            $model->preferred_datetime ? Carbon::parse($model->preferred_datetime)->format('m/d/Y g:i a') : '')
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
        return [
            Filter::inputText('instructor_name', 'instructors.display_name')
                ->operators(['contains']),

            Filter::inputText('librarian_name', 'librarians.display_name')
            ->operators(['contains']),

//            Filter::inputText('campus_name', 'campuses.name')
//                ->operators(['contains']),

            Filter::select('campus_name', 'campuses.code')
                ->dataSource([
                    ['value' => 'OL', 'label' => 'Online'],
                    ['value' => 'CAS', 'label' => 'Cascade'],
                    ['value' => 'RC', 'label' => 'Rock Creek'],
                    ['value' => 'SE', 'label' => 'Southeast'],
                    ['value' => 'SY', 'label' => 'Sylvania'],
                    ['value' => 'O', 'label' => 'Other'],

                ])
                ->optionValue('value')
                ->optionLabel('label'),


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

            Filter::datepicker('created_at', 'instruction_requests.created_at'),
            Filter::datepicker('instruction_datetime'),
        ];
    }

    /**
     * Define the action buttons for each row
     */
    public function actionsFromView($row): View
    {
        return view('components.table-actions', [
            'id' => $row->id,
            'editRoute' => 'instructionRequests.edit',
            'deleteEvent' => 'confirmDelete',
            'canEdit' => true,
            'canDelete' => true,
            'size' => 'w-4 h-4',
            'routeKeyName' => 'instructionRequest'
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
        InstructionRequests::destroy($id);
        $this->dispatch('pg:eventRefresh-' . $this->tableName);
    }


    #[\Livewire\Attributes\On('filterByLibrarian')]
    public function filterByLibrarian($name): void
    {
        $this->filters['input_text']['librarians.display_name'] = $name;
        $this->dispatch('pg:eventRefresh-' . $this->tableName);
    }

    #[\Livewire\Attributes\On('filterByCampus')]
    public function filterByCampus($code): void
    {
        $this->filters['select']['campuses.code'] = $code;
        $this->dispatch('pg:eventRefresh-' . $this->tableName);
    }

    #[\Livewire\Attributes\On('filterByStatus')]
    public function filterByStatus($status): void
    {
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
                'clearFilters'
            ]
        );
    }
}
