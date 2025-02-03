<?php

namespace App\Livewire;

use App\Models\Campus;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use Illuminate\View\View;

final class CampusTable extends PowerGridComponent
{
    use WithExport;

    public string $tableName = 'campuses';
    public string $primaryKey = 'campuses.id';

    public function setUp(): array
    {
        //$this->showCheckBox();
        return [
//            PowerGrid::header()->showSearchInput(),
            PowerGrid::footer()->showPerPage()->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Campus::query();
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('name')
            ->add('code')
            ->add('librarians', function (Campus $model) {
                $librarianIds = is_array($model->librarian_ids) ? $model->librarian_ids : [];
                $librarians = User::whereIn('id', $librarianIds)->pluck('display_name');
                return $librarians->map(fn($name) =>
                "<span class=\"inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-300 text-green-800\">$name</span>"
                )->implode(' ');
            })
            ->add('gcal');
    }

    public function columns(): array
    {
        return [
            Column::make('Campus', 'name')
                ->searchable()
                ->sortable(),

            Column::make('Code', 'code')
                ->searchable()
                ->sortable(),

            Column::make('Notifications to:', 'librarians'),

            Column::make('GCal URL', 'gcal')
                ->searchable(),

            Column::action('Action')
        ];
    }

    public function actionsFromView(Campus $row): View
    {
        return view('components.table-actions', [
            'id' => $row->id,
            'editRoute' => 'campuses.edit',
            'deleteEvent' => 'confirmDelete',
            'canEdit' => true,
            'canDelete' => false,
            'size' => 'w-4 h-4',
            'routeKeyName' => 'campus'
        ]);
    }

    #[\Livewire\Attributes\On('confirmDelete')]
    public function confirmDelete($id): void
    {
        $this->js('confirm("Are you sure you want to delete this campus?") && $wire.delete(' . $id . ')');
    }

    #[\Livewire\Attributes\On('delete')]
    public function delete($id): void
    {
        Campus::destroy($id);
        $this->dispatch('pg:eventRefresh-' . $this->tableName);
    }

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
