<?php

namespace App\DataTables;

use App\Models\InstructionRequest;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;

class InstructionRequestDataTable extends DataTable
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

        return $dataTable
            ->addColumn('action', 'instruction_requests.datatables_actions')
            ->editColumn('instructor_name', function ($row) {
                return '<a href="' . route('instructionRequests.edit', $row->id) . '" title="click to edit"><i class="fa fa-edit"></i> ' . $row->instructor_name . '</a>';
            })
            ->editColumn('preferred_datetime', function ($row) {
                // Format the preferred date as "Thu, Feb 01 - 1:30pm"
                return Carbon::parse($row->preferred_datetime)->format('D, M d - g:ia');
            })
            // Add similar editColumn calls for other columns
            ->rawColumns(['action', 'instructor_name']); // Specify which columns contain raw HTML

    }



    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\InstructionRequest $model
     * @return Builder
     */
    public function query(InstructionRequest $model)
    {
        return $model->newQuery()
            ->leftJoin('instructors', 'instruction_requests.instructor_id', '=', 'instructors.id')
            ->leftJoin('classes', 'instruction_requests.class_id', '=', 'classes.id')
            ->leftJoin('instruction_request_details', 'instruction_requests.id', '=', 'instruction_request_details.instruction_request_id')
            ->leftJoin('users as librarians', 'instruction_request_details.librarian_id', '=', 'librarians.id')
            ->leftJoin('campuses', 'instruction_requests.campus_id', '=', 'campuses.id') // Adjust this line
            ->select(
                'instruction_requests.*',
                'instructors.display_name as instructor_name',
                'instructors.email as instructor_email',
                'classes.course_name',
                'librarians.display_name as librarian_name',
                'instruction_requests.status',
                'instruction_request_details.created_by',
                'instruction_requests.preferred_datetime',
                'campuses.name as campus_name' // Include this line
            );
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
            ['name' => 'instructor_name', 'title' => 'Instructor Name', 'data' => 'instructor_name'],
//            ['name' => 'instructor_email', 'title' => 'Instructor Email', 'data' => 'instructor_email'],
            ['name' => 'instruction_type', 'title' => 'Type', 'data' => 'instruction_type'],
            ['name' => 'course_modality', 'title' => 'Modality', 'data' => 'course_modality'],
            ['name' => 'librarian_name', 'title' => 'Librarian', 'data' => 'librarian_name'],
            ['name' => 'campus_name', 'title' => 'Campus', 'data' => 'campus_name'],
            ['name' => 'course_name', 'title' => 'Course Name', 'data' => 'course_name'],
            ['name' => 'status', 'title' => 'Status', 'data' => 'status'],
//            ['name' => 'created_by', 'title' => 'Created By', 'data' => 'created_by'],
            ['name' => 'preferred_datetime', 'title' => 'Preferred Date', 'data' => 'preferred_datetime'],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'instruction_requests_datatable_' . time();
    }
}
