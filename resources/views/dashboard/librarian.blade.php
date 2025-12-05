@extends('layouts.app')

@section('content')
    <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 rounded-lg">
        <p class="text-green-800 dark:text-green-200 font-semibold">
            🚀 New Feature:Librarian Dashboard - We're actively building this! Try it out and let us know what you think.
        </p>
    </div>

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
            'iconBgColor' => 'bg-blue-500 dark:bg-blue-700',
            'icon' => 'clipboard-document-list',
            'infoBoxText' => 'My Active Requests',
            'count' => $myActiveRequestsCount
        ],
        [
            'iconBgColor' => 'bg-purple-500 dark:bg-purple-700',
            'icon' => 'document-text',
            'infoBoxText' => 'All Received Requests',
            'count' => $receivedRequestsCount
        ],
        [
            'iconBgColor' => 'bg-green-500 dark:bg-green-700',
            'icon' => 'check-circle',
            'infoBoxText' => 'Completed This Term',
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
    <div class="mt-4"
         @open-schedule-modal.window="console.log('Dashboard: Caught open-schedule-modal event, dispatching Livewire event', $event.detail); Livewire.dispatch('openScheduleModal', $event.detail)"
         @mark-in-progress.window="console.log('Dashboard: Caught mark-in-progress event, dispatching Livewire event', $event.detail); Livewire.dispatch('markInProgress', $event.detail)"
         @mark-complete.window="console.log('Dashboard: Caught mark-complete event, dispatching Livewire event', $event.detail); Livewire.dispatch('markComplete', $event.detail)">

        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">My Active Requests</h2>
        <livewire:my-active-requests-table />
    </div>

    {{-- Table 3: Recently Received (Full Width, Green header) - FUNCTIONAL --}}
    {{-- FUNCTIONAL: This PowerGrid table shows ALL received requests (not filtered by librarian)
         with filtering capabilities matching InstructionRequestTable pattern.
    --}}
    <div class="mt-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">All Received Requests</h2>
        <livewire:recently-received-table />
    </div>

    {{-- Calendar Placeholder --}}
    <div class="mt-4 w-full rounded-md overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="px-4 py-8 bg-gray-50 dark:bg-gray-800 text-center">
            <x-heroicon-o-calendar class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4" />
            <p class="text-base font-medium text-gray-700 dark:text-gray-300">Calendar Component</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Month view showing scheduled sessions will appear here</p>
        </div>
    </div>

@endsection

{{-- Import table-specific lock refresh script for auto-unlock functionality --}}
@push('scripts')
    @vite(['resources/js/table-lock-refresh.js'])
@endpush
