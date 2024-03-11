@extends('layouts.app')

@section('content')

    <x-page-header title="Classes" text="Edit and manage classes.">
            <a class="btn btn-primary float-right"
               href="{{ route('classes.create') }}">
                Add New
            </a>
    </x-page-header>

    <div class="content px-3">

        @include('flash::message')

        <div class="clearfix"></div>

        <div class="card">
            <div class="card-body p-0">
                @include('classes.table')
            </div>

        </div>
    </div>

@endsection

