@extends('layouts.app')

@section('header')
    <x-breadcrumbs :breadcrumbs="[
        ['label' => 'Dashboard']
    ]" />
    <h1 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:text-3xl sm:truncate">
        Dashboard
    </h1>
    <p class="text-gray-600 dark:text-gray-300">Manage instruction requests assigned to you.
        <span class="text-gray-400 dark:text-gray-500">•</span>
        Also try:
        <a href="{{ route('dashboard.librarian') }}" class="text-cyan-600 dark:text-cyan-400 hover:text-cyan-700 dark:hover:text-cyan-300 hover:underline">Librarian Dashboard</a>
        <span class="text-gray-400 dark:text-gray-500">•</span>
        <a href="{{ route('dashboard.scheduler') }}" class="text-cyan-600 dark:text-cyan-400 hover:text-cyan-700 dark:hover:text-cyan-300 hover:underline">Scheduler Dashboard</a>
    </p>
@endsection

@section('content')
    <x-status-bar
        :items="[
        [
            'iconBgColor' => 'bg-amber-500 dark:bg-amber-700',  // Matches assigned table header
            'icon' => 'user-group',
            'infoBoxText' => 'Assigned to '.Auth::user()->display_name,
            'count' => $myAssignedRequests->count() ?: 0
        ],
        [
            'iconBgColor' => 'bg-fuchsia-500 dark:bg-fuchsia-700',  // Matches accepted table header
            'icon' => 'star',
            'infoBoxText' => 'Accepted by '.Auth::user()->display_name,
            'count' => $myAcceptedRequests->count() ?: 0
        ],
        [
            'iconBgColor' => 'bg-blue-500 dark:bg-blue-700',  // Matches received table header
            'icon' => 'document-text',
            'infoBoxText' => 'Received Requests',
            'count' => $receivedRequests->count() ?: 0
        ],
        [
            'iconBgColor' => 'bg-green-500 dark:bg-green-700',  // For completed items
            'icon' => 'check-circle',
            'infoBoxText' => 'Completed',
            'count' => $completedRequests->count() ?: 0
        ]
    ]"
    />
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-4">
        <div class="col-span-12 xl:col-span-5 flex flex-col gap-4"> {{-- Column 1: Assigned (4 columns) --}}
            <x-instruction-request-table
                :instructionRequests="$myAssignedRequests"
                title="Assigned to {{ Auth::user()->display_name }}"
                headerBgColor="bg-amber-500 dark:bg-amber-700" {{-- Distinct dark mode header color --}}
                :showStatus="false"
                :showCampus="true"
                :showClass="true"
                :showInstructor="true"
                :showLibrarian="false"
                :showDate="true"
                :compact="true"
            />

            <x-instruction-request-table
                :instructionRequests="$myAcceptedRequests"
                title="Accepted by {{ Auth::user()->display_name }}"
                headerBgColor="bg-fuchsia-500 dark:bg-fuchsia-700" {{-- Distinct dark mode header color --}}
                :showStatus="false"
                :showClass="true"
                :showInstructor="true"
                :showCampus="true"
                :showLibrarian="false"
                :showDate="true"
                :compact="true"
            />
        </div>

        <div class="col-span-12 xl:col-span-7 h-full"> {{-- Column 2: Received (8 columns) --}}
            <x-instruction-request-table
                :instructionRequests="$receivedRequests"
                title="Recently Received Requests"
                headerBgColor="bg-blue-500 dark:bg-blue-700" {{-- Distinct dark mode header color --}}
                :showStatus="true"
                :showClass="true"
                :showInstructor="true"
                :showCampus="true"
                :showDate="true"
                :compact="false"
                :showFooter="true"
            />
        </div>
    </div>


@endsection
