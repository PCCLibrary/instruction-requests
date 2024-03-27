@extends('layouts.app')

@section('content')
    <x-page-header title="Campuses" text="Edit and manage campuses, and assign librarians to notify when new requests arrive.">
        <a class="btn btn-primary float-right"
           href="{{ route('campuses.create') }}">
            Add New
        </a>
    </x-page-header>


    <div class="content px-3">

        @include('flash::message')

        <div class="clearfix"></div>

        <div class="card">
            <div class="card-body p-0">
                @include('campuses.table')
            </div>

        </div>
    </div>

@endsection

