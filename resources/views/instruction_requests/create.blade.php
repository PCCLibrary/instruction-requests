@extends('layouts.app')

@section('content')

    <div class="content px-3">

        @include('adminlte-templates::common.errors')

        {!! Form::open(['route' => 'instructionRequests.store']) !!}

        <div class="row">

                <div class="col-md-9 pt-4">
                    @include('instruction_requests.fields')
                </div>

                <div class="col-md-3 pt-4">
                    <x-editor-actions
                        route="{{ route('instructionRequests.index') }}"
                        :showBack="false"
                        title="Create New Instruction Request"
                    />
                </div>

        </div>

        {!! Form::close() !!}

    </div>
@endsection
