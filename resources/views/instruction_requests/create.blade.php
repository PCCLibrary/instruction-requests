@extends('layouts.app')

@section('content')

    <div class="content px-3">

        @include('adminlte-templates::common.errors')

        {!! Form::open(['route' => 'instructionRequests.store']) !!}

        <div class="row mt-4">

                <div class="col-md-9 card">
                    <div class="card-body">
                        @include('instruction_requests.fields')
                    </div>
                </div>

                <div class="col-md-3">
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
