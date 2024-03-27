@extends('layouts.app')

@push('third_party_stylesheets')

<style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #e0ffe1;
        border: 1px solid #aaa;
        border-radius: 4px;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__display {
        cursor: default;
        padding: 1em .5em;
        color: #000;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover, .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:focus {
        background-color: transparent;
        color: red;
        outline: none;
    }
</style>
@endpush

@push('third_party_scripts')
    <script>
        $('#librarian_ids').select2({
            // theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Select Librarian'
        });
    </script>
@endpush

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1>Edit Campus</h1>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('adminlte-templates::common.errors')

        <div class="card">

            {!! Form::model($campus, ['route' => ['campuses.update', $campus->id], 'method' => 'patch']) !!}

            <div class="card-body">
                <div class="row">
                    @include('campuses.fields')
                </div>
            </div>

            <div class="card-footer">
                {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
                <a href="{{ route('campuses.index') }}" class="btn btn-default">Cancel</a>
            </div>

            {!! Form::close() !!}

        </div>
    </div>
@endsection

