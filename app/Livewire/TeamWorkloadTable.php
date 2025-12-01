<?php

namespace App\Livewire;

use App\Services\LibrarianWorkloadService;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;

final class TeamWorkloadTable extends PowerGridComponent
{
    use WithExport;

    public string $tableName = 'team_workload';
    public string $primaryKey = 'id';
    public string $sortField = 'display_name';
    public string $sortDirection = 'asc';

    public $campus = null;

    protected $listeners = ['campusFilterUpdated' => 'updateCampusFilter'];

    /**
     * Configure theme color before PowerGrid processes it
     * Uses service container binding to inject pre-configured theme instance
     */
    public function mount(): void
    {
        $themeClass = $this->customThemeClass() ?? strval(config('livewire-powergrid.theme'));

        // Create and configure theme instance
        $theme = new $themeClass();
        $theme->header_color = 'bg-purple-500 dark:bg-purple-600'; // Match filter panel

        // Bind this specific configured instance into the service container
        // When parent::mount() calls app($themeClass), it will get THIS instance
        app()->instance($themeClass, $theme);

        // Now parent can safely call app($themeClass)->apply() and get our configured theme
        parent::mount();
    }

    public function updateCampusFilter($campus)
    {
        // Convert empty string to null
        $this->campus = $campus === '' ? null : $campus;
    }

    public function setUp(): array
    {
        return [
            PowerGrid::header(),

            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),

            PowerGrid::exportable('team_workload_' . now()->format('Y-m-d'))
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV)
                ->striped()
                ->csvDelimiter(',')
                ->csvSeparator(','),
        ];
    }

    public function datasource(): Collection
    {
        $service = app(LibrarianWorkloadService::class);

        // Convert empty string to null for service call
        $campusFilter = $this->campus === '' ? null : $this->campus;

        $data = $service->getWorkloadStatistics($campusFilter);

        if ($this->sortField && $this->sortDirection) {
            $data = $data->sortBy(
                $this->sortField,
                SORT_REGULAR,
                $this->sortDirection === 'desc'
            )->values();
        }

        return $data;
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('display_name')
            ->add('campus_name')
            ->add('total_active')
            ->add('assigned_count')
            ->add('accepted_count')
            ->add('scheduled_count')
            ->add('async_count')
            ->add('in_person_count')
            ->add('remote_count')
            ->add('librarian_display', function($row) {
                $html = '<div>' . e($row->display_name) . '</div>';
                if (!empty($row->campus_name)) {
                    $html .= '<div class="text-sm text-gray-500 dark:text-gray-400">' . e($row->campus_name) . '</div>';
                }
                return $html;
            });
    }

    public function columns(): array
    {
        return [
            Column::make('Librarian', 'librarian_display', 'display_name')
                ->sortable()
                ->searchable(),

            Column::make('Total Active', 'total_active')
                ->sortable()
                ->bodyAttribute('class', 'font-semibold text-center'),

            Column::make('In-Person', 'in_person_count')
                ->sortable()
                ->bodyAttribute('class', 'text-center'),

            Column::make('Remote', 'remote_count')
                ->sortable()
                ->bodyAttribute('class', 'text-center'),

            Column::make('Async', 'async_count')
                ->sortable()
                ->bodyAttribute('class', 'text-center'),

            Column::make('Assigned', 'assigned_count')
                ->sortable()
                ->bodyAttribute('class', 'text-center'),

            Column::make('Accepted', 'accepted_count')
                ->sortable()
                ->bodyAttribute('class', 'text-center'),

            Column::make('Scheduled / In Progress', 'scheduled_count')
                ->sortable()
                ->bodyAttribute('class', 'text-center'),

            Column::action('Action')
                ->visibleInExport(false)
                ->headerAttribute('class', 'w-24')
                ->bodyAttribute('class', 'w-24'),
        ];
    }

    /**
     * Define filters for the table
     */
    public function filters(): array
    {
        return [
            Filter::inputText('display_name')
                ->operators(['contains']),
        ];
    }

    /**
     * Define action buttons for each row
     */
    public function actionsFromView($row): View
    {
        return view('components.table-actions', [
            'id' => $row->id,
            'editRoute' => 'users.edit',
            'canEdit' => true,
            'canDelete' => false,  // No delete button for workload table
            'size' => 'w-4 h-4',
            'routeKeyName' => 'user',
        ]);
    }
}
