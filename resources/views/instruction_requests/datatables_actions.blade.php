{!! Form::open(['route' => ['instructionRequests.destroy', $id], 'method' => 'delete']) !!}
<div class='btn-group'>
    <a href="{{ route('instructionRequests.show', $id) }}" class='btn btn-default btn-xs'>
        <i class="fa fa-eye"></i>
    </a>
    <a href="{{ route('instructionRequests.edit', $id) }}" class='btn btn-default btn-xs'>
        <i class="fa fa-edit"></i>
    </a>
    {!! Form::button('<i class="fa fa-trash"></i>', [
        'type' => 'submit',
        'class' => 'btn btn-danger btn-xs',
        'onclick' => "return confirm('Are you sure?')"
    ]) !!}

    <a href="{{ route('instructionRequests.copy', $id) }}" class='btn btn-info btn-xs'>
        <i class="fa fa-copy"></i>
    </a>
</div>
{!! Form::close() !!}
