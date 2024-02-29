<div class="table-responsive">
    <table class="table" id="instructionRequestDetails-table">
        <thead>
        <tr>
            <th>Instruction Request Id</th>
        <th>Librarian Id</th>
        <th>Created By</th>
        <th>Last Updated By</th>
            <th colspan="3">Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach($instructionRequestDetails as $instructionRequestDetails)
            <tr>
                <td>{{ $instructionRequestDetails->instruction_request_id }}</td>
            <td>{{ $instructionRequestDetails->librarian_id }}</td>
            <td>{{ $instructionRequestDetails->created_by }}</td>
            <td>{{ $instructionRequestDetails->last_updated_by }}</td>
                <td>
                    {!! Form::open(['route' => ['instructionRequestDetails.destroy', $instructionRequestDetails->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('instructionRequestDetails.show', [$instructionRequestDetails->id]) }}"
                           class='btn btn-default btn-xs'>
                            <i class="far fa-eye"></i>
                        </a>
                        <a href="{{ route('instructionRequestDetails.edit', [$instructionRequestDetails->id]) }}"
                           class='btn btn-default btn-xs'>
                            <i class="far fa-edit"></i>
                        </a>
                        {!! Form::button('<i class="far fa-trash-alt"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
