{{-- resources/views/instruction-requests/index.blade.php --}}
@extends('layouts.app')

@section('content')
    <x-page-header>
        <x-slot:title>Instruction Requests</x-slot:title>
        <x-slot:text>Manage or create instruction requests.</x-slot:text>

        @can('create', App\Models\InstructionRequest::class)
            <x-slot:actions>
                <a href="{{ route('instructionRequests.create') }}"
                   class="btn btn-success">
                    <i class="fa fa-plus"></i> Add New
                </a>
            </x-slot:actions>
        @endcan
    </x-page-header>

    <div class="content px-3">
        <div class="card">
            <div class="card-body p-0">
                @include('instruction_requests.partials.filters')
                @include('instruction_requests.partials.table')
            </div>
        </div>
    </div>
@endsection
