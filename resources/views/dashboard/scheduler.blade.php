@extends('layouts.app')

@section('content')
    <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 rounded-lg">
        <p class="text-green-800 dark:text-green-200 font-semibold">
            🚀 New Feature:Scheduler Dashboard - We're actively building this! Try it out and let us know what you think.
        </p>
    </div>

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

    {{-- Calendar Placeholder --}}
    <div class="mt-4 w-full rounded-md overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="px-4 py-8 bg-gray-50 dark:bg-gray-800 text-center">
            <x-heroicon-o-calendar class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4" />
            <p class="text-base font-medium text-gray-700 dark:text-gray-300">Team Calendar Component</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Month view showing all librarian schedules will appear here</p>
        </div>
    </div>

@endsection

{{-- Import table-specific lock refresh script for auto-unlock functionality --}}
@push('scripts')
    @vite(['resources/js/table-lock-refresh.js'])
@endpush
