@extends('layouts.app')

@section('content')
    <div class="mb-4 p-4 bg-yellow-100 dark:bg-yellow-900 border border-yellow-400 dark:border-yellow-700 rounded-lg">
        <p class="text-yellow-800 dark:text-yellow-200 font-semibold">
            🎨 MOCKUP: Librarian Dashboard - This is a static preview of the proposed design
        </p>
    </div>

    {{-- Status Bar --}}
    <x-status-bar
        :items="[
        [
            'iconBgColor' => 'bg-amber-500 dark:bg-amber-700',
            'icon' => 'clock',
            'infoBoxText' => 'Pending Your Response',
            'count' => 12
        ],
        [
            'iconBgColor' => 'bg-blue-500 dark:bg-blue-700',
            'icon' => 'clipboard-document-list',
            'infoBoxText' => 'My Active Requests',
            'count' => 8
        ],
        [
            'iconBgColor' => 'bg-green-500 dark:bg-green-700',
            'icon' => 'check-circle',
            'infoBoxText' => 'Completed This Term',
            'count' => 45
        ]
    ]"
    />

    {{-- Table 1: Assigned to Me (Full Width, Amber header) --}}
    {{-- PLACEHOLDER: Will be implemented as a simple table component showing status='assigned' for current librarian --}}
    <div class="mt-4 w-full rounded-md overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between bg-amber-500 dark:bg-amber-700 px-4 py-2">
            <h3 class="text-sm font-medium text-white">Assigned to Me</h3>
            <span class="text-xs text-white">10 most recent</span>
        </div>

        <div class="px-4 py-8 bg-gray-50 dark:bg-gray-800 text-center">
            <x-heroicon-o-clipboard-document-list class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4" />
            <p class="text-base font-medium text-gray-700 dark:text-gray-300">Assigned to Me Table</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Simple table component showing status='assigned' for current librarian</p>
        </div>
    </div>

    {{-- Table 2: My Active Requests (Full Width, Blue header) --}}
    {{-- PLACEHOLDER: Will be implemented as PowerGrid with Google Calendar modal and conditional action buttons --}}
    <div class="mt-4 w-full rounded-md overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between bg-blue-500 dark:bg-blue-700 px-4 py-2">
            <h3 class="text-sm font-medium text-white">My Active Requests</h3>
        </div>

        <div class="px-4 py-8 bg-gray-50 dark:bg-gray-800 text-center">
            <x-heroicon-o-briefcase class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4" />
            <p class="text-base font-medium text-gray-700 dark:text-gray-300">My Active Requests Table</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">PowerGrid showing accepted/scheduled/in_progress for current librarian with action buttons</p>
        </div>
    </div>

    {{-- Table 3: Recently Received (Full Width, Green header) - FUNCTIONAL --}}
    {{-- FUNCTIONAL: This PowerGrid table shows ALL received requests (not filtered by librarian)
         with filtering capabilities matching InstructionRequestTable pattern.
    --}}
    <div class="mt-4">
        <div class="mb-2">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Received Requests</h2>
        </div>
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
