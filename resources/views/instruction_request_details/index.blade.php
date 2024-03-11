@extends('layouts.app')

@section('content')
    <x-page-header title="Instruction Requests Details" text="Do not create new details - only used for testing and debugging.">
        <a class="btn btn-primary float-right"
           href="{{ route('instructionRequestDetails.create') }}">
            Add New
        </a>
    </x-page-header>

    <div class="content px-3">

        @include('flash::message')

        <div class="clearfix"></div>

        <div class="card">
            <div class="card-body p-0">
                @include('instruction_request_details.table')
            </div>

        </div>
    </div>

@endsection

