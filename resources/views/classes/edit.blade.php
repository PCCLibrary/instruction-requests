@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="text-gray-900 dark:text-white">Edit Classes</h1>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('adminlte-templates::common.errors')

        <div class="card dark:bg-gray-800">

            {!! Form::model($classes, ['route' => ['classes.update', $classes->id], 'method' => 'patch']) !!}

            <div class="card-body dark:bg-gray-800">
                <div class="row">
                    @include('classes.fields')
                </div>
            </div>

            <div class="card-footer dark:bg-gray-800 dark:border-gray-700">
                {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
                <a href="{{ route('classes.index') }}" class="btn btn-default dark:text-gray-300">Cancel</a>
            </div>

            {!! Form::close() !!}

        </div>
    </div>
@endsection
