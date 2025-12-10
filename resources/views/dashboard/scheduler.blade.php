@extends('layouts.app')

@section('header')
    <x-breadcrumbs :breadcrumbs="[
        ['label' => 'Scheduler Dashboard']
    ]" />
    <h1 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:text-3xl sm:truncate">
        Scheduler Dashboard
    </h1>
    <p class="text-gray-600 dark:text-gray-300">
        Manage team workload and request assignments.
        <span class="text-gray-400 dark:text-gray-500">•</span>
        <a href="{{ route('dashboard.librarian') }}" class="text-cyan-600 dark:text-cyan-400 hover:text-cyan-700 dark:hover:text-cyan-300 hover:underline">Librarian Dashboard</a>
    </p>
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

    {{-- Sticky Jump Navigation --}}
    <div class="sticky top-0 z-40 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-4 py-3 mb-4 mt-4">
        <nav class="flex space-x-6 text-sm font-medium">
            <a href="#calendar" class="text-gray-600 dark:text-gray-300 hover:text-cyan-600 dark:hover:text-cyan-400 transition">
                📅 Calendar
            </a>
            <a href="#team-workload" class="text-gray-600 dark:text-gray-300 hover:text-cyan-600 dark:hover:text-cyan-400 transition">
                👥 Team Workload
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
            :allow-librarian-select="true"
        />
    </div>

    {{-- Section Divider --}}
    <hr class="my-8 border-gray-200 dark:border-gray-700" />

    {{-- Team Workload Section --}}
    <div id="team-workload">
        {{-- Team Workload Filter --}}
        <livewire:team-workload-filter />

        {{-- Team Workload Table --}}
        <div class="mt-4">
            <livewire:team-workload-table />
        </div>
    </div>

    {{-- Section Divider --}}
    <hr class="my-8 border-gray-200 dark:border-gray-700" />

    {{-- All Requests Section --}}
    <div id="all-requests">
        {{-- Unified Request Filters --}}
        <livewire:request-filters />

        {{-- Unified Instruction Request Table --}}
        <div class="mt-4">
            <livewire:instruction-request-table />
        </div>
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

@endsection

{{-- Import table-specific lock refresh script for auto-unlock functionality --}}
@push('scripts')
    @vite(['resources/js/table-lock-refresh.js'])
@endpush
