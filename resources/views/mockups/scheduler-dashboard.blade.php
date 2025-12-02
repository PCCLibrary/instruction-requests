@extends('layouts.app')

@section('content')
    <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 rounded-lg">
        <p class="text-green-800 dark:text-green-200 font-semibold">
            🚀 New Feature: Team Workload Dashboard - We're actively building this! Try it out and let us know what you think.
        </p>
    </div>

    {{-- Status Bar --}}
    <x-status-bar
        :items="[
        [
            'iconBgColor' => 'bg-red-500 dark:bg-red-700',
            'icon' => 'inbox',
            'infoBoxText' => 'Unassigned Requests',
            'count' => 15
        ],
        [
            'iconBgColor' => 'bg-yellow-500 dark:bg-yellow-700',
            'icon' => 'exclamation-triangle',
            'infoBoxText' => 'Needs Attention',
            'count' => 23
        ],
        [
            'iconBgColor' => 'bg-blue-500 dark:bg-blue-700',
            'icon' => 'clipboard-document-list',
            'infoBoxText' => 'Total Active Requests',
            'count' => 156
        ]
    ]"
    />

    {{-- Team Workload Filter --}}
    <livewire:team-workload-filter />

    {{-- Team Workload Table --}}
    <div class="mt-4">
        <livewire:team-workload-table />
    </div>

    {{-- Scheduler Request Filters --}}
    <livewire:scheduler-request-filters />

    {{-- Scheduler Request Table --}}
    <div class="mt-4">
        <livewire:scheduler-request-table />
    </div>

    {{-- Calendar Placeholder --}}
    <div class="mt-4 w-full rounded-md overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="px-4 py-8 bg-gray-50 dark:bg-gray-800 text-center">
            <x-heroicon-o-calendar class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4" />
            <p class="text-base font-medium text-gray-700 dark:text-gray-300">Team Calendar Component</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Month view showing all librarian schedules will appear here</p>
        </div>
    </div>

@endsection
