@extends('layouts.app')

@section('page-title', 'Librarian Dashboard')
@section('page-description', 'Manage instruction requests assigned to you.')

@section('page-nav')
    <a href="#calendar" class="inline-flex items-center gap-1.5 text-gray-600 hover:text-cyan-600 dark:text-gray-400 dark:hover:text-cyan-400 py-3">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
        </svg>
        Calendar
    </a>
    <a href="#my-assigned" class="inline-flex items-center gap-1.5 text-gray-600 hover:text-cyan-600 dark:text-gray-400 dark:hover:text-cyan-400 py-3">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        My Assigned
    </a>
    <a href="#my-active" class="inline-flex items-center gap-1.5 text-gray-600 hover:text-cyan-600 dark:text-gray-400 dark:hover:text-cyan-400 py-3">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        My Active
    </a>
    <a href="#all-requests" class="inline-flex items-center gap-1.5 text-gray-600 hover:text-cyan-600 dark:text-gray-400 dark:hover:text-cyan-400 py-3">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
        </svg>
        All Requests
    </a>
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

    {{-- Calendar Component --}}
    <div class="mt-4 scroll-mt-44 lg:scroll-mt-36" id="calendar">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Instruction Requests Calendar</h2>
        <livewire:dashboard-calendar
            :user-id="auth()->id()"
            :allow-librarian-select="false"
        />
    </div>

    {{-- Table 1: Assigned to Me (Full Width, Amber header) - FUNCTIONAL --}}
    <div class="mt-8 scroll-mt-44 lg:scroll-mt-36" id="my-assigned">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">My Assigned Requests</h2>
        <livewire:assigned-to-me-table />
    </div>

    {{-- Table 2: My Active Requests (Blue PowerGrid header with Google Calendar modal) --}}
    <div class="mt-8 scroll-mt-44 lg:scroll-mt-36" id="my-active">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">My Active Requests</h2>
        <livewire:my-active-requests-table />
    </div>

    {{-- Table 3: Instruction Requests (Unified table with filters) --}}
    <div class="mt-8 scroll-mt-44 lg:scroll-mt-36" id="all-requests">
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
