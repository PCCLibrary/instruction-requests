@extends('layouts.app')

@section('header')
    <x-breadcrumbs :breadcrumbs="[
        ['label' => 'Librarian Dashboard']
    ]" />
    <h1 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:text-3xl sm:truncate">
        Librarian Dashboard
    </h1>
    <p class="text-gray-600 dark:text-gray-300">Manage instruction requests assigned to you.</p>

    <div class="mt-2 p-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 rounded-lg">
        <p class="text-green-800 dark:text-green-200 font-semibold">
            🚀 New Feature:Librarian Dashboard - We're actively building this! Try it out and let us know what you think.
        </p>
    </div>
@endsection

@section('content')
    {{-- Status Bar --}}
    <x-status-bar
        :items="[
        [
            'iconBgColor' => 'bg-amber-500 dark:bg-amber-700',
            'icon' => 'clock',
            'infoBoxText' => 'Assigned to Me',
            'count' => $assignedToMeCount
        ],
        [
            'iconBgColor' => 'bg-teal-500 dark:bg-teal-700',
            'icon' => 'clipboard-document-list',
            'infoBoxText' => 'My Active Requests',
            'count' => $myActiveRequestsCount
        ],
        [
            'iconBgColor' => 'bg-blue-500 dark:bg-blue-700',
            'icon' => 'document-text',
            'infoBoxText' => 'All Received Requests',
            'count' => $receivedRequestsCount
        ],
        [
            'iconBgColor' => 'bg-green-500 dark:bg-green-700',
            'icon' => 'check-circle',
            'infoBoxText' => 'Completed',
            'count' => $completedRequestsCount
        ]
    ]"
    />

    {{-- Table 1: Assigned to Me (Full Width, Amber header) - FUNCTIONAL --}}
    <div class="mt-4">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">My Assigned Requests</h2>
        <livewire:assigned-to-me-table />
    </div>


    {{-- Table 2: My Active Requests (Blue PowerGrid header with Google Calendar modal) --}}
    <div class="mt-4">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">My Active Requests</h2>
        <livewire:my-active-requests-table />
    </div>

    {{-- Table 3: Instruction Requests (Unified table with filters) --}}
    <div class="mt-4">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">All Instruction Requests</h2>
        <livewire:request-filters />
        <livewire:instruction-request-table />
    </div>

    {{-- Calendar Component --}}
    <div class="mt-4">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">My Schedule</h2>
        <livewire:dashboard-calendar
            :user-id="auth()->id()"
            :allow-librarian-select="false"
        />
    </div>

    {{-- Wrapper component for modal management --}}
    <livewire:librarian-dashboard-wrapper />
@endsection

{{-- Import table-specific lock refresh script for auto-unlock functionality --}}
@push('scripts')
    @vite(['resources/js/table-lock-refresh.js'])
@endpush
