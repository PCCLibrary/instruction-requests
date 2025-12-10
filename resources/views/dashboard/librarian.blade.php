@extends('layouts.app')

@section('header')
    <x-breadcrumbs :breadcrumbs="[
        ['label' => 'Librarian Dashboard']
    ]" />
    <h1 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:text-3xl sm:truncate">
        Librarian Dashboard
    </h1>
    <p class="text-gray-600 dark:text-gray-300">
        Manage instruction requests assigned to you.
    </p>
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

    {{-- Sticky Jump Navigation --}}
    <div class="sticky top-0 z-40 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-4 py-3 mb-4 mt-4">
        <nav class="flex space-x-6 text-sm font-medium">
            <a href="#calendar" class="text-gray-600 dark:text-gray-300 hover:text-cyan-600 dark:hover:text-cyan-400 transition">
                📅 Calendar
            </a>
            <a href="#my-assigned" class="text-gray-600 dark:text-gray-300 hover:text-cyan-600 dark:hover:text-cyan-400 transition">
                ⏰ My Assigned
            </a>
            <a href="#my-active" class="text-gray-600 dark:text-gray-300 hover:text-cyan-600 dark:hover:text-cyan-400 transition">
                ✓ My Active
            </a>
            <a href="#all-requests" class="text-gray-600 dark:text-gray-300 hover:text-cyan-600 dark:hover:text-cyan-400 transition">
                📋 All Requests
            </a>
        </nav>
    </div>

    {{-- Calendar Component --}}
    <div class="mt-4" id="calendar">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Instruction Requests Calendar</h2>
        <livewire:dashboard-calendar
            :user-id="auth()->id()"
            :allow-librarian-select="false"
        />
    </div>

    {{-- Section Divider --}}
    <hr class="my-8 border-gray-200 dark:border-gray-700" />

    {{-- Table 1: Assigned to Me (Full Width, Amber header) - FUNCTIONAL --}}
    <div class="mt-4" id="my-assigned">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">My Assigned Requests</h2>
        <livewire:assigned-to-me-table />
    </div>

    {{-- Table 2: My Active Requests (Blue PowerGrid header with Google Calendar modal) --}}
    <div class="mt-4" id="my-active">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">My Active Requests</h2>
        <livewire:my-active-requests-table />
    </div>

    {{-- Section Divider --}}
    <hr class="my-8 border-gray-200 dark:border-gray-700" />

    {{-- Table 3: Instruction Requests (Unified table with filters) --}}
    <div class="mt-4" id="all-requests">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">All Instruction Requests</h2>
        <livewire:request-filters />
        <livewire:instruction-request-table />
    </div>

    {{-- Back to Top Button --}}
    <button
        x-data="{ show: false }"
        x-show="show"
        @scroll.window="show = window.pageYOffset > 400"
        @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
        class="fixed bottom-6 right-6 p-3 bg-cyan-600 dark:bg-cyan-500 text-white rounded-full shadow-lg hover:bg-cyan-700 dark:hover:bg-cyan-600 transition z-50"
        style="display: none;"
    >
        ↑
    </button>

    {{-- Wrapper component for modal management --}}
    <livewire:librarian-dashboard-wrapper />
@endsection

{{-- Import table-specific lock refresh script for auto-unlock functionality --}}
@push('scripts')
    @vite(['resources/js/table-lock-refresh.js'])
@endpush
