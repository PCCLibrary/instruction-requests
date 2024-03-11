@extends('layouts.app')

@section('content')

    <x-page-header title="Instruction Requests" text="Manage or create instruction requests.">
                    <a class="btn btn-success float-right"
                       href="{{ route('instructionRequests.create') }}">
                        Add New
                    </a>
    </x-page-header>


    <div class="content px-3">

        @include('flash::message')

        <div class="clearfix"></div>

        <div class="card">
            <div class="card-body p-0">
                @include('instruction_requests.table')
            </div>

        </div>
    </div>

@endsection

