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

final class UserTable extends PowerGridComponent
{
    public string $tableName = 'users';
    public string $primaryKey = 'users.id';

    // Filter properties to control visibility
    public bool $showActiveUsers = true;
    public bool $showDeletedUsers = false;

    public function setUp(): array
    {
        //$this->showCheckBox();

        return [
            PowerGrid::header()
                ->includeViewOnTop('users.partials.user-filters'),

            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        $query = User::query()->with('campuses')->select('users.*');

        // Always filter admin users unless they're available for assignment
        $query->where(function ($q) {
            $q->where('is_admin', false)
              ->orWhere(function ($subQ) {
                  $subQ->where('is_admin', true)
                       ->where('available_for_assignment', true);
              });
        });

        // Apply soft delete filters
        if (!$this->showActiveUsers && !$this->showDeletedUsers) {
            // If neither is selected, show nothing (empty result)
            return $query->whereRaw('1 = 0');
        } elseif ($this->showActiveUsers && !$this->showDeletedUsers) {
            // Show only active users (default behavior)
            return $query;
        } elseif (!$this->showActiveUsers && $this->showDeletedUsers) {
            // Show only deleted users
            return $query->onlyTrashed();
        } else {
            // Show both active and deleted users
            return $query->withTrashed();
        }
    }

    public function updatedShowActiveUsers()
    {
        $this->dispatch('pg:eventRefresh-' . $this->tableName);
    }

    public function updatedShowDeletedUsers()
    {
        $this->dispatch('pg:eventRefresh-' . $this->tableName);
    }

    // Alternative methods if using wire:click="toggleActiveUsers" instead of $toggle
    public function toggleActiveUsers()
    {
        $this->showActiveUsers = !$this->showActiveUsers;
        $this->dispatch('pg:eventRefresh-' . $this->tableName);
    }

    public function toggleDeletedUsers()
    {
        $this->showDeletedUsers = !$this->showDeletedUsers;
        $this->dispatch('pg:eventRefresh-' . $this->tableName);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('display_name', function (User $model) {
                // Add visual indicator for soft-deleted users
                if ($model->trashed()) {
                    return '<span class="text-gray-400 line-through">' . e($model->display_name) . '</span> <span class="text-red-500 text-xs">(Deleted)</span>';
                }
                return e($model->display_name);
            })
            ->add('email', function (User $model) {
                $emailHtml = '<a href="mailto:' . $model->email . '" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    ' . $model->email . '
                </a>';

                // Add visual indicator for soft-deleted users
                if ($model->trashed()) {
                    return '<div class="text-gray-400">' . $emailHtml . '</div>';
                }
                return $emailHtml;
            })
            ->add('created_at')
            ->add('created_at_formatted', fn (User $model) =>
            Carbon::parse($model->created_at)->format('m/d/Y g:i a'))
            ->add('campuses', function (User $model) {
                return $model->campuses->map(fn($campus) =>
                    "<span class=\"inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-sky-600 text-white\">{$campus->code}</span>"
                )->implode(' ');
            });
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

            Column::make('Campuses', 'campuses'),

            Column::action('Action')
                ->visibleInExport(false)
                ->headerAttribute('class', 'w-24')
                ->bodyAttribute('class', 'w-24')
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
        // Check if user is soft-deleted
        $isTrashed = $row->trashed();

        return view('components.table-actions', [
            'id' => $row->id,
            'editRoute' => 'users.edit',
            'deleteEvent' => $isTrashed ? null : 'confirmDelete', // Disable delete for already deleted users
            'restoreEvent' => $isTrashed ? 'confirmRestore' : null, // Add restore for deleted users
            'canEdit' => !$isTrashed, // Disable edit for soft-deleted users
            'canDelete' => !$isTrashed, // Disable delete for soft-deleted users
            'canRestore' => $isTrashed, // Enable restore for soft-deleted users
            'size' => 'w-4 h-4',
            'routeKeyName' => 'user',
            'confirmMessage' => $isTrashed ? 'Are you sure you want to restore this user?' : 'Are you sure you want to delete this user?'
        ]);
    }

    #[\Livewire\Attributes\On('confirmDelete')]
    public function confirmDelete($id): void
    {
        // Directly call delete without showing a second confirmation dialog
        $this->delete($id);
    }

    #[\Livewire\Attributes\On('delete')]
    public function delete($id): void
    {
        User::destroy($id);
        $this->dispatch('pg:eventRefresh-' . $this->tableName);
    }

    #[\Livewire\Attributes\On('confirmRestore')]
    public function confirmRestore($id): void
    {
        // Directly call restore without showing a second confirmation dialog
        $this->restore($id);
    }

    #[\Livewire\Attributes\On('restore')]
    public function restore($id): void
    {
        $user = User::withTrashed()->find($id);
        if ($user && $user->trashed()) {
            $user->restore();
            $this->dispatch('pg:eventRefresh-' . $this->tableName);
        }
    }

    protected function getListeners()
    {
        return array_merge(
            parent::getListeners(),
            [
                'confirmDelete',
                'delete',
                'confirmRestore',
                'restore',
                'userUpdated' => '$refresh',
            ]
        );
    }
}
