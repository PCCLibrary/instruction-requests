{{-- resources/views/instruction-requests/index.blade.php --}}
@extends('layouts.app')

@section('content')
    <x-page-header>
        <x-slot:title>Instruction Requests</x-slot:title>
        <x-slot:text>Manage or create instruction requests.</x-slot:text>

        @can('create', App\Models\InstructionRequests::class)
            <x-slot:actions>
                <a href="{{ route('instructionRequests.create') }}"
                   class="btn btn-success">
                    <i class="fa fa-plus"></i> Add New
                </a>
            </x-slot:actions>
        @endcan
    </x-page-header>

    <div class="content px-3">

    @include('instruction-requests.partials.filters')
    @include('instruction-requests.partials.table')

    </div>
@endsection
