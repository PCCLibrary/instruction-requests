@extends('layouts.app')

@section('content')
    <x-status-bar
        :headerBgColor="'bg-sky-500 dark:bg-sky-700'" {{-- Distinct dark mode header color --}}
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
