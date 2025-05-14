{{-- resources/views/instruction-requests/index.blade.php --}}
@extends('layouts.app')

@section('header')
        <x-breadcrumbs :breadcrumbs="[
            ['label' => 'Instruction Requests', 'route' => 'instructionRequests.index']
            ]" />


        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:text-3xl sm:truncate">
                    Instruction Requests
                </h1>
                <p class="mt-2 text-base text-gray-600 dark:text-gray-300">
                    Manage or create instruction requests.
                </p>
            </div>

            <a href="{{ route('instructionRequests.create') }}"
               class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 rounded-md font-semibold text-xs text-white uppercase tracking-widest transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-4 w-4 mr-2"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M12 4v16m8-8H4" />
                </svg>
                Create New Request
            </a>
        </div>
@endsection

@section('content')

    <livewire:request-filters />

    @include('instruction-requests.partials.table')

@endsection
