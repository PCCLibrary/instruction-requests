@extends('layouts.app')

@section('content')

    <x-page-header title="Instructors" text="Manage instructors">
            <a class="btn btn-success float-right"
               href="{{ route('instructors.create') }}">
                Add New
            </a>
    </x-page-header>



    <div class="content px-3">

        @include('flash::message')

        <div class="clearfix"></div>

        <div class="card">
            <div class="card-body p-0">
                @include('instructors.table')
            </div>

        </div>
    </div>

@endsection

