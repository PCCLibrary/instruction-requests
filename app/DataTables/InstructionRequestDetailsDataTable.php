<?php

namespace App\DataTables;

use App\Models\InstructionRequest;
use App\Models\InstructionRequestDetails;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;

class InstructionRequestDetailsDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return DataTableAbstract
     */

    public function dataTable($query)
    {
        $dataTable = new EloquentDataTable($query);

        return $dataTable->addColumn('action', 'instruction_request_details.datatables_actions');

    }


    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\InstructionRequestDetails $model
     * @return Builder
     */
    public function query(InstructionRequestDetails $model): Builder
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
                'buttons'   => [
                    ['extend' => 'create', 'className' => 'btn btn-default btn-sm no-corner',],
                    ['extend' => 'export', 'className' => 'btn btn-default btn-sm no-corner',],
                    ['extend' => 'print', 'className' => 'btn btn-default btn-sm no-corner',],
                ],
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
            ['name' => 'id', 'title' => 'Detail ID', 'data' => 'id'],
            ['name' => 'instruction_request_id', 'title' => 'Request ID', 'data' => 'instruction_request_id'],
            ['name' => 'assigned_librarian_id', 'title' => 'Librarian', 'data' => 'assigned_librarian_id'],
//            ['name' => 'created_by', 'title' => 'Created By', 'data' => 'created_by'],
            ['name' => 'instruction_duration', 'title' => 'Instruction Duration', 'data' => 'instruction_duration'],
            ['name' => 'class_notes', 'title' => 'Class Notes', 'data' => 'class_notes'],
            ['name' => 'research_guide', 'title' => 'Research Guide', 'data' => 'research_guide'],
            ['name' => 'video', 'title' => 'Video', 'data' => 'video'],
            ['name' => 'embedded', 'title' => 'Embedded', 'data' => 'embedded'],
//            ['name' => 'other', 'title' => 'Other', 'data' => 'other'],
//            ['name' => 'materials', 'title' => 'Materials', 'data' => 'materials'],
//            ['name' => 'assessment_notes', 'title' => 'Assessment Notes', 'data' => 'assessment_notes'],
//            ['name' => 'assessments', 'title' => 'Assessments', 'data' => 'assessments'],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'instruction_requests_details_datatable_' . time();
    }
}
