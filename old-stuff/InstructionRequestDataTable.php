<?php

namespace App\DataTables;

use App\Models\InstructionRequest;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\DataTables;

class InstructionRequestDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return DataTableAbstract
     */
    public function dataTable($query): DataTableAbstract
    {
        $dataTable = new EloquentDataTable($query);

        return $dataTable
            ->addColumn('action', 'instruction_requests.datatables_actions')
            ->editColumn('instructor_name', function ($row) {
                return $row->instructor_name;
            })
            ->editColumn('preferred_datetime', function ($row) {
                return Carbon::parse($row->preferred_datetime)->format('m/d/Y g:i a');
            })
            ->editColumn('created_at', function ($row) {
                $formattedDate = Carbon::parse($row->created_at)->format('m/d/Y g:i a');
                return '<a href="' . route('instructionRequests.edit', $row->id) . '" title="click to manage"><i class="fa fa-edit"></i>' . $formattedDate . '</a>';
            })
            ->rawColumns(['action', 'created_at']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param InstructionRequest $model
     * @return Builder
     */
    public function query(InstructionRequest $model)
    {
        $query = $model->newQuery()
            ->leftJoin('instructors', 'instruction_requests.instructor_id', '=', 'instructors.id')
            ->leftJoin('classes', 'instruction_requests.class_id', '=', 'classes.id')
            ->leftJoin('instruction_request_details', 'instruction_requests.id', '=', 'instruction_request_details.instruction_request_id')
            ->leftJoin('users as librarians', 'instruction_request_details.assigned_librarian_id', '=', 'librarians.id')
            ->leftJoin('campuses', 'instruction_requests.campus_id', '=', 'campuses.id')
            ->select(
                'instruction_requests.*',
                'instructors.display_name as instructor_name',
                'instructors.email as instructor_email',
                'classes.course_name',
                'librarians.display_name as librarian_name',
                'instruction_requests.status',
                'instruction_request_details.created_by',
                'campuses.name as campus_name'
            );

        // Correct the static call to eloquent()
        return app('datatables')->eloquent($query)
            ->filterColumn('instructor_name', function ($query, $keyword) {
                $query->whereRaw("LOWER(instructors.display_name) LIKE ?", ["%{$keyword}%"]);
            })
            ->filter(function ($query) {
                if (request()->has('search') && $search = request('search')['value']) {
                    $query->where(function ($query) use ($search) {
                        $query->orWhereRaw("LOWER(instructors.display_name) LIKE ?", ["%{$search}%"]);
                    });
                }
            });
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
                'order'     => [[0, 'desc']], // Set initial order to the 'Created At' column
                'buttons'   => [
                    ['extend' => 'create', 'className' => 'btn btn-success btn-sm no-corner',],
                    ['extend' => 'export', 'className' => 'btn btn-yellow btn-sm no-corner',],
                    ['extend' => 'print', 'className' => 'btn btn-info btn-sm no-corner',],
                    ['extend' => 'reload', 'className' => 'btn btn-default btn-sm no-corner',],
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
            ['name' => 'created_at', 'title' => 'Submitted', 'data' => 'created_at', 'searchable' => false, 'orderable' => true],
            ['name' => 'instructor_name', 'title' => 'Instructor Name', 'data' => 'instructor_name'],
            ['name' => 'instruction_type', 'title' => 'Type', 'data' => 'instruction_type'],
            ['name' => 'librarian_name', 'title' => 'Librarian', 'data' => 'librarian_name'],
            ['name' => 'campus_name', 'title' => 'Campus', 'data' => 'campus_name'],
            ['name' => 'course_name', 'title' => 'Course Name', 'data' => 'course_name'],
            ['name' => 'status', 'title' => 'Status', 'data' => 'status'],
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
