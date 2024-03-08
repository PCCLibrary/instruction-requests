@extends('layouts.app')

@section('content')
    <div class="content px-3">

        @include('flash::message')

        <div class="clearfix"></div>

        @include('adminlte-templates::common.errors')

        {!! Form::model($instructionRequest, ['route' => ['instructionRequests.update', $instructionRequest->id], 'method' => 'patch']) !!}

        <div class="row mt-4">

            <div class="col-md-9">
                <x-card title="Requesting Instructor" headerclass="bg-lightblue">
                    @include('instruction_requests.admin.contact')
                </x-card>

                <x-card title="Instruction Request Information" headerclass="bg-purple">
                    @include('instruction_requests.fields')
                </x-card>
            </div>

            <div class="col-md-3">
                <x-card title="Edit Instruction Request"
                        headerclass="bg-green">
                    @include('instruction_requests.admin.editor')
                </x-card>

                <x-card title="Manage" headerclass="bg-info">
                    @include('instruction_requests.admin.fields')
                </x-card>

                {{--                            <pre>{{ print_r(json_encode($instructionRequest->detail), true) }}</pre>--}}
            </div>

        </div>

        {!! Form::close() !!}
    </div>
@endsection
