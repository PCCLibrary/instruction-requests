@extends('layouts.app')

@section('content')
    <div class="content px-3">

        @include('flash::message')

        <div class="clearfix"></div>

        @include('adminlte-templates::common.errors')

        {!! Form::model($instructionRequest, ['route' => ['instructionRequests.update', $instructionRequest->id], 'method' => 'patch']) !!}

        @if($instructionRequest->status == 'assigned')
            @include('instruction_requests.admin.accept')
        @endif

        <div class="row mt-4">

            <div class="col-md-4">
                <x-card title="Edit Instruction Request"
                        headerclass="bg-lightblue">
                    @include('instruction_requests.admin.editor')
                </x-card>


                <x-card title="Manage" headerclass="bg-purple">
                    @include('instruction_requests.admin.fields')
                </x-card>


                <x-card title="File Attachments"
                        headerclass="bg-blue">
                    @include('instruction_requests.admin.file_attachments')
                </x-card>

            </div>

            <div class="col-md-8">
                @include('instruction_requests.admin.admin_view')

                <x-card title="Notes"
                        headerclass="bg-green">

                @include('instruction_requests.admin.notes')

                </x-card>
            </div>

        </div>

        {!! Form::close() !!}
    </div>
@endsection
