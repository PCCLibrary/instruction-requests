<?php

namespace App\Livewire;

use App\Models\User;
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

final class UserTable extends PowerGridComponent
{
    use WithExport;

    public string $tableName = 'users';
    public string $primaryKey = 'users.id';

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::header()
                ->showSearchInput(),

            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),

            (new Exportable('users_' . now()->format('Y-m-d')))
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV)
                ->stripTags(true)
                ->csvDelimiter('"')
                ->csvSeparator(','),
        ];
    }

    public function datasource(): Builder
    {
        return User::query()
            ->with('campus')
            ->select('users.*');
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('display_name')
            ->add('email', function (User $model) {
                return '<a href="mailto:' . $model->email . '" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    ' . $model->email . '
                </a>';
            })
            ->add('created_at')
            ->add('created_at_formatted', fn (User $model) =>
            Carbon::parse($model->created_at)->format('m/d/Y g:i a'));
    }

    public function columns(): array
    {
        return [
            Column::make('Name', 'display_name')
                ->searchable()
                ->sortable(),

            Column::make('Email', 'email')
                ->searchable()
                ->sortable(),

            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('display_name')
                ->operators(['contains']),

            Filter::inputText('email')
                ->operators(['contains']),

        ];
    }

    public function actionsFromView(User $row): View
    {
        return view('components.table-actions', [
            'id' => $row->id,
            'editRoute' => 'users.edit',
            'deleteEvent' => 'confirmDelete',
            'canEdit' => true,
            'canDelete' => true,
            'size' => 'w-4 h-4',
            'routeKeyName' => 'user'
        ]);
    }

    #[\Livewire\Attributes\On('confirmDelete')]
    public function confirmDelete($id): void
    {
        $this->js('confirm("Are you sure you want to delete this user?") && $wire.delete(' . $id . ')');
    }

    #[\Livewire\Attributes\On('delete')]
    public function delete($id): void
    {
        User::destroy($id);
        $this->dispatch('pg:eventRefresh-' . $this->tableName);
    }

    protected function getListeners()
    {
        return array_merge(
            parent::getListeners(),
            [
                'confirmDelete',
                'delete',
                'userUpdated' => '$refresh',
            ]
        );
    }
}
