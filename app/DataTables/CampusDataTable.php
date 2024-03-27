<?php

namespace App\DataTables;

use App\Models\Campus;
use App\Models\User;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;

class CampusDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * Resolves librarian IDs to display names and adds them as a new column.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $dataTable = new EloquentDataTable($query);

        $dataTable->addColumn('action', 'campuses.datatables_actions');

        // Manually resolve librarian IDs to display names
        $dataTable->addColumn('librarians', function ($campus) {
            $librarianIds = json_decode($campus->librarian_ids, true);
            if (!empty($librarianIds)) {
                $librarians = User::whereIn('id', $librarianIds)->pluck('display_name')->toArray();
                // Wrap each name in a span tag with the class "rounded-pill small"
                $librariansHtml = array_map(function($name) {
                    return "<span class=\"badge-success px-2 py-1 rounded-pill small\">$name</span>";
                }, $librarians);
                return implode(' ', $librariansHtml); // Use a space or any other separator as needed
            }
            return '';
        })->rawColumns(['librarians', 'action']); // Specify the columns that should be treated as raw HTML

        return $dataTable;
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Campus $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Campus $model)
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->addAction(['width' => '120px', 'printable' => false])
            ->parameters([
                'dom'       => 'Bfrtip',
                'stateSave' => true,
                'order'     => [[0, 'desc']],
                'buttons'   => [],
                'searching' => false,
                'paging'    => false,
//                'info'      => false
            ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            'name' => ['title' => 'Campus'],
            'code',
            'librarians' => ['name' => 'librarians', 'data' => 'librarians', 'title' => 'Notifications to:', 'searchable' => false, 'orderable' => false],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'campuses_datatable_' . time();
    }
}
