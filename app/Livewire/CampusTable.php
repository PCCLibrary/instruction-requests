<?php

namespace App\Livewire;

use App\Models\Campus;
use App\Models\User;
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

final class CampusTable extends PowerGridComponent
{
    use WithExport;

    public string $tableName = 'campuses';
    public string $primaryKey = 'campuses.id';

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

            (new Exportable('campuses_' . now()->format('Y-m-d')))
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
        return Campus::query();
    }

    /**
     * Define all fields that will be used in the table and exports
     */
    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('code')
            ->add('librarians', function (Campus $model) {
                $librarianIds = json_decode($model->librarian_ids, true);
                if (!empty($librarianIds)) {
                    $librarians = User::whereIn('id', $librarianIds)->pluck('display_name')->toArray();
                    return collect($librarians)->map(function($name) {
                        return "<span class=\"inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800\">$name</span>";
                    })->implode(' ');
                }
                return '';
            })
            ->add('gcal')
            ->add('created_at')
            ->add('created_at_formatted', fn (Campus $model) =>
            Carbon::parse($model->created_at)->format('m/d/Y g:i a'));
    }

    /**
     * Define the table columns configuration
     */
    public function columns(): array
    {
        return [
            Column::add()
                ->title('Campus')
                ->field('name')
                ->searchable()
                ->sortable(),

            Column::add()
                ->title('Code')
                ->field('code')
                ->searchable()
                ->sortable(),

            Column::add()
                ->title('Notifications to:')
                ->field('librarians'),

            Column::add()
                ->title('GCal URL')
                ->field('gcal')
                ->searchable()
                ->sortable(),

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
            Filter::inputText('code'),
            Filter::inputText('gcal'),
        ];
    }

    /**
     * Define the action buttons for each row
     */
    public function actions(Campus $row): array
    {
        return [
            Button::add('edit')
                ->slot('<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>')
                ->class('inline-flex items-center px-2 py-1 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150')
                ->route('campuses.edit', ['campus' => $row->id]),  // Fixed parameter name

            Button::add('delete')
                ->slot('<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>')
                ->class('inline-flex items-center px-2 py-1 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150')
                ->dispatch('confirmDelete', ['campus' => $row->id])  // Fixed parameter name
        ];
    }

    /**
     * Handle delete confirmation
     */
    #[\Livewire\Attributes\On('confirmDelete')]
    public function confirmDelete($campus): void  // Fixed parameter name
    {
        $this->js('confirm("Are you sure you want to delete this campus?") && $wire.delete(' . $campus . ')');
    }

    /**
     * Handle delete action
     */
    #[\Livewire\Attributes\On('delete')]
    public function delete($campus): void  // Fixed parameter name
    {
        Campus::destroy($campus);
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
                'campusUpdated' => '$refresh',
            ]
        );
    }
}
