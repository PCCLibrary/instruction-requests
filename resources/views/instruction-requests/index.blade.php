{{-- resources/views/instruction-requests/index.blade.php --}}
@extends('layouts.app')

@section('page-title', 'Instruction Requests')
@section('page-description', 'Manage or create instruction requests.')

@section('page-nav')
    <div class="flex-1"></div>
    <a href="{{ route('instructionRequests.create') }}"
       class="inline-flex items-center gap-1.5 text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300 font-medium py-3">
        <svg xmlns="http://www.w3.org/2000/svg"
             class="h-4 w-4"
             fill="none"
             viewBox="0 0 24 24"
             stroke="currentColor"
             stroke-width="2">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M12 4v16m8-8H4" />
        </svg>
        Create New Request
    </a>
@endsection

@section('content')

    <livewire:request-filters />

    @include('instruction-requests.partials.table')

@endsection
