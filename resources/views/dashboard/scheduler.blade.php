@extends('layouts.app')

@section('header')
    <x-breadcrumbs :breadcrumbs="[
        ['label' => 'Scheduler Dashboard']
    ]" />
    <h1 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:text-3xl sm:truncate">
        Librarian Dashboard
    </h1>
    <p class="text-gray-600 dark:text-gray-300">Manage instruction requests assigned to you.</p>

    <div class="mt-2 p-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 rounded-lg">
        <p class="text-green-800 dark:text-green-200 font-semibold">
            🚀 New Feature:Scheduler Dashboard - We're actively building this! Try it out and let us know what you think.
        </p>
    </div>
@endsection

@section('content')
    {{-- Status Bar --}}
    <x-status-bar
        :items="[
        [
            'iconBgColor' => 'bg-blue-500 dark:bg-blue-700',
            'icon' => 'document-text',
            'infoBoxText' => 'Received Requests',
            'count' => $receivedCount
        ],
        [
            'iconBgColor' => 'bg-amber-500 dark:bg-amber-700',
            'icon' => 'clock',
            'infoBoxText' => 'Expiring Soon',
            'count' => $expiringSoonCount
        ],
        [
            'iconBgColor' => 'bg-rose-500 dark:bg-rose-700',
            'icon' => 'arrow-path',
            'infoBoxText' => 'Rejected Requests',
            'count' => $rejectedCount
        ],
        [
            'iconBgColor' => 'bg-teal-500 dark:bg-teal-700',
            'icon' => 'clipboard-document-check',
            'infoBoxText' => 'Active Requests',
            'count' => $activeWorkCount
        ]
    ]"
    />

    {{-- Team Workload Filter --}}
    <livewire:team-workload-filter />

    {{-- Team Workload Table --}}
    <div class="mt-4">
        <livewire:team-workload-table />
    </div>

    {{-- Unified Request Filters --}}
    <livewire:request-filters />

    {{-- Unified Instruction Request Table --}}
    <div class="mt-4">
        <livewire:instruction-request-table />
    </div>

    {{-- Calendar Component --}}
    <div class="mt-4">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Team Schedule</h2>
        <livewire:dashboard-calendar
            :allow-librarian-select="true"
        />
    </div>

@endsection

{{-- Import table-specific lock refresh script for auto-unlock functionality --}}
@push('scripts')
    @vite(['resources/js/table-lock-refresh.js'])
@endpush
