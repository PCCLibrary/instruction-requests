<?php

namespace App\Livewire;

use App\Models\Instructor;
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

/**
 *
 */
final class InstructorTable extends PowerGridComponent
{
    use WithExport;

    /**
     * @var string
     */
    public string $tableName = 'instructors';
    /**
     * @var string
     */
    public string $primaryKey = 'instructors.id';

    /**
     * Configure the table setup including export functionality
     */
    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::header()
                ->showSearchInput(),

            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),

            (new Exportable('instructors_' . now()->format('Y-m-d')))
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV)
                ->stripTags(true)
                ->csvDelimiter('"')
                ->csvSeparator(','),
        ];
    }

    /**
     * Define the data source query
     */
    public function datasource(): Builder
    {
        return Instructor::query();
    }

    /**
     * Define all fields that will be used in the table and exports
     */
    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('display_name')
            ->add('pronouns')
            ->add('email', function (Instructor $model) {
                return '<a href="mailto:' . $model->email . '" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    ' . $model->email . '
                </a>';
            })
            ->add('phone')
            ->add('created_at')
            ->add('created_at_formatted', fn (Instructor $model) =>
            Carbon::parse($model->created_at)->format('m/d/Y g:i a'));
    }

    /**
     * Define the table columns configuration
     */
    public function columns(): array
    {
        return [
            Column::make('Name','name')
                ->searchable()
                ->sortable(),

            Column::make('Display Name', 'display_name')
                ->searchable()
                ->sortable(),

            Column::make('Pronouns','pronouns'),
//                ->searchable(false)
//                ->sortable(false),

            Column::make('Email','email'),
//                ->searchable()
//                ->sortable(),

            Column::make('Phone','phone'),
//                ->searchable()
//                ->sortable(),

            Column::action('Action')
        ];
    }

    /**
     * Define the available filters for the table
     */
    public function filters(): array
    {
        return [
            Filter::inputText('name'),
            Filter::inputText('display_name'),
        ];
    }


    /**
     * Define the action buttons for each row
     * @param Instructor $row
     * @return View
     */
    public function actionsFromView(Instructor $row): View
    {
        return view('components.table-actions', [
            'id' => $row->id,
            'editRoute' => 'instructors.edit',
            'deleteEvent' => 'confirmDelete',
            'canEdit' => true,
            'canDelete' => true,
            'size' => 'w-4 h-4',
            'routeKeyName' => 'instructor'
        ]);
    }

    /**
     * Handle delete confirmation
     * @param $id
     */
    #[\Livewire\Attributes\On('confirmDelete')]
    public function confirmDelete($id): void
    {
        $this->js('confirm("Are you sure you want to delete this instructor?") && $wire.delete(' . $id . ')');
    }

    #[\Livewire\Attributes\On('delete')]
    public function delete($id): void
    {
        Instructor::destroy($id);
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
                'instructorUpdated' => '$refresh',
            ]
        );
    }
}
