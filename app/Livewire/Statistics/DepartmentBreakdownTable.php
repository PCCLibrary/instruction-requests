<?php

namespace App\Livewire\Statistics;

use Illuminate\Support\Collection;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;

final class DepartmentBreakdownTable extends PowerGridComponent
{
    use WithExport;

    public string $tableName = 'department_breakdown_table';
    public string $sortField = 'sessions';
    public string $sortDirection = 'desc';

    // Receives pre-computed aggregated data from parent component
    public Collection $departmentData;

    /**
     * Configure PowerGrid theme to match statistics reports
     */
    public function mount(): void
    {
        // Set purple header color to match filter panel
        $themeClass = $this->customThemeClass() ?? strval(config('livewire-powergrid.theme'));
        $theme = new $themeClass();
        $theme->header_color = 'bg-purple-500 dark:bg-purple-600';
        app()->instance($themeClass, $theme);

        parent::mount();
    }

    /**
     * Configure the table setup
     */
    public function setUp(): array
    {
        return [
            PowerGrid::header()
                ->showSearchInput(false), // Disable search - parent handles filtering

            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),

            (new Exportable('department_breakdown_' . now()->format('Y-m-d')))
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV)
                ->striped()
        ];
    }

    /**
     * PowerGrid datasource - convert array items to objects for property access
     *
     * PowerGrid works better with objects. Convert associative arrays to stdClass
     * so PowerGrid can access properties (e.g., $row->department) consistently.
     */
    public function datasource(): Collection
    {
        return $this->departmentData->map(fn($item) => (object) $item);
    }

    /**
     * Define all fields for display and export
     */
    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('department')
            ->add('sessions')
            ->add('students')
            ->add('avg_class_size', fn ($row) => number_format($row->avg_class_size, 1))
            ->add('percentage', fn ($row) => number_format($row->percentage, 1) . '%');
    }

    /**
     * Define the table columns with sorting
     */
    public function columns(): array
    {
        return [
            Column::make('Department', 'department')
                ->sortable(),

            Column::make('Sessions', 'sessions')
                ->sortable(),

            Column::make('Students', 'students')
                ->sortable(),

            Column::make('Avg Class Size', 'avg_class_size')
                ->sortable(),

            Column::make('% of Total', 'percentage')
                ->sortable(),
        ];
    }
}
