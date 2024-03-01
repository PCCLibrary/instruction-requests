@extends('layouts.app')

@section('content')
    <section class="content-header">

    <div class="content px-3">

        @include('adminlte-templates::common.errors')

            {!! Form::model($instructionRequest, ['route' => ['instructionRequests.update', $instructionRequest->id], 'method' => 'patch']) !!}

                    <div class="row">

                        <div class="col-md-9 pt-4">
                            @include('instruction_requests.fields')
                        </div>

                        <div class="col-md-3 pt-4">
                            <x-editor-actions
                                route="{{ route('instructionRequests.index') }}"
                                :showBack="false"
                                title="Edit Instruction Request"
                            />
                        </div>

                    </div>

{{--                <div class="row">--}}

{{--                    <div class="col-md-3 pt-4">--}}
{{--                        <div class="card">--}}
{{--                            <div class="card-body row">--}}
{{--                            @include('instruction_requests.fields')--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}

{{--                    <div class="col-md-3 pt-4">--}}
{{--                        <x-editor-actions--}}
{{--                            route="{{ route('instructionRequests.index') }}"--}}
{{--                            :showBack="true"--}}
{{--                            title="Edit Instruction Request"--}}

{{--                        />--}}
{{--                    </div>--}}

{{--                </div>--}}

            {!! Form::close() !!}
    </div>
@endsection
