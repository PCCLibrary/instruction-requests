@extends('layouts.app')

@section('content')
    <div class="content px-3">

{{--        <pre>{{ print_r(json_encode($instructionRequest), true) }}</pre>--}}


        @include('adminlte-templates::common.errors')

            {!! Form::model($instructionRequest, ['route' => ['instructionRequests.update', $instructionRequest->id], 'method' => 'patch']) !!}

                    <div class="row mt-4">

                        <div class="col-md-9 card">
                            <div class="card-body">

                                @include('instruction_requests.fields')
                            </div>
                        </div>

                        <div class="col-md-3">
                            <x-card title="Edit Instruction Request"
                                    classes="bg-lightblue">
                                <x-editor-actions
                                    route="{{ route('instructionRequests.index') }}"
                                    :showBack="true"
                                />

                                <x-input-select name="status"
                                                label="Request Status"
                                                :options="['pending' => 'Pending', 'copied' => 'Copied', 'in progress' => 'In Progress', 'completed' => 'Completed']"
                                                :selected="old('status', $instructionRequest->status ?? null)"
                                                classes="col-12 p-0 m-0 mt-4"
                                />

                                <ul class="list-unstyled mt-4">

                                    <li><strong>created by: </strong>{{ $instructionRequest->detail->created_by }}</li>
                                    <li><strong>last updated by: </strong>{{ $instructionRequest->detail->last_updated_by }}</li>
                                    <li><strong>on: </strong>{{ \Carbon\Carbon::parse($instructionRequest->detail->updated_at)->format('M d g:i A') }}</li>

                                </ul>
                            </x-card>


                            <x-card title="Manage" classes="bg-info">

                                @include('instruction_requests.admin_fields')
                            </x-card>

{{--                            <pre>{{ print_r(json_encode($instructionRequest->detail), true) }}</pre>--}}
                        </div>

                    </div>


            {!! Form::close() !!}
    </div>
@endsection
