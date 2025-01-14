{{--<div class='d-flex justify-content-start align-content-center'>--}}

<div class='d-flex justify-content-evenly'>

{{--        <a href="{{ route('instructionRequests.show', $id) }}" class='btn btn-xs mr-1 btn-info'>--}}
{{--            <i class="fa fa-eye"></i>--}}
{{--        </a>--}}

    <a href="{{ route('instructionRequests.edit', $id) }}" class='btn btn-success btn-xs mr-1'>
        <i class="fa fa-edit"></i>
    </a>
    {!! Form::open(
        [
            'route' => ['instructionRequests.destroy', $id],
            'method' => 'delete',
            'class' => 'p-0 m-0 d-inline-block'
        ]
    ) !!}

    {!! Form::button('<i class="fa fa-trash"></i>', [
        'type' => 'submit',
        'class' => 'btn btn-danger btn-xs',
        'onclick' => "return confirm('Are you sure?')"
    ]) !!}

    {!! Form::close() !!}

{{--    {!! Form::open(['route' => ['instructionRequests.copy', $id], 'method' => 'get']) !!}--}}

{{--    {!! Form::button('<i class="fa fa-copy"></i>', [--}}
{{--        'type' => 'submit',--}}
{{--        'class' => 'btn btn-info btn-xs',--}}
{{--        'onclick' => "return confirm('Are you sure you want to duplicate this request?')"--}}
{{--    ]) !!}--}}

{{--    {!! Form::close() !!}--}}

{{--    <a href="{{ route('instructionRequests.copy', $id) }}" class='btn btn-info btn-xs'>--}}
{{--        <i class="fa fa-copy"></i>--}}
{{--    </a>--}}
</div>
