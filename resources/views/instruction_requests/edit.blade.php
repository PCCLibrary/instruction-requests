@extends('layouts.app')

@push('page_css')
    <style>
        .select2-container--default .select2-selection--multiple .select2-selection__choice__display {
            padding-left: 1.5em;
            padding-right: .5em;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            background-color: transparent;
            border: none;
            border-right: 1px solid #aaa;
            border-top-left-radius: 4px;
            border-bottom-left-radius: 4px;
            color: #000;
            cursor: pointer;
            font-size: 1em;
            font-weight: bold;
            padding: 0 4px;
            position: absolute;
            left: 0;
            top: 0;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #dfeeff;
            border-color: #006fe6;
            color: #000000;
            padding: 0 1em;
            margin-top: .31rem;
        }
    </style>
@endpush

@section('content')
    <div class="content px-3">

        @include('flash::message')

        <div class="clearfix"></div>

        @include('adminlte-templates::common.errors')

        @if($instructionRequest->status == 'assigned' && $instructionRequest->detail->assigned_librarian_id == Auth::user()->id)
            @include('instruction_requests.admin.accept')
        @endif

        {!! Form::model($instructionRequest, ['route' => ['instructionRequests.update', $instructionRequest->id], 'method' => 'patch']) !!}

        <div class="row mt-4">

            <div class="col-md-4">
                <x-card title="Manage"
                        headerclass="bg-lightblue">
                    @include('instruction_requests.admin.editor')
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

        <x-card title="Comments" headerclass="bg-lightblue">
            @comments(['model' => $instructionRequest])
        </x-card>

    </div>


@endsection

@push('third_party_scripts')
    <script>
        $('#assigned_librarian_id').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Select Librarian'
        });

            $(document).ready(function() {
                $('#tasks-completed').select2();

                $('form').on('submit', function(event) {
                    let selectedTasks = $('#tasks-completed').val();
                    let tasks = ['video', 'non_video', 'modified_tutorial', 'embedded', 'research_guide', 'handout', 'developed_assigment', 'other_materials'];

                    // Remove existing hidden inputs for tasks
                    tasks.forEach(function(task) {
                        $('input[name="' + task + '"]').remove();
                    });

                    // Create hidden inputs for tasks
                    tasks.forEach(function(task) {
                        var value = selectedTasks.includes(task) ? 1 : 0;
                        $('<input>').attr({
                            type: 'hidden',
                            name: task,
                            value: value
                        }).appendTo('form');
                    });
                });
        });

    </script>
@endpush
