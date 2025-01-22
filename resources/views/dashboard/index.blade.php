@extends('layouts.app')

@section('content')
    {{-- Status Boxes --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
        <x-status-box
            :bgClass="'bg-blue-100 border-l-4 border-blue-400 text-blue-700'"
            :icon="'fa fa-file'"
            :infoBoxText="'Received'"
            :count="$pendingRequests->count() ?: 0"
        />
        <x-status-box
            :bgClass="'bg-amber-100 border-l-4 border-amber-400 text-amber-700'"
            :icon="'fa fa-user'"
            :infoBoxText="'Assigned'"
            :count="$inProgressRequests->count() ?: 0"
        />
        <x-status-box
            :bgClass="'bg-fuchsia-100 border-l-4 border-fuchsia-400 text-fuchsia-700'"
            :icon="'fa fa-star'"
            :infoBoxText="'Accepted'"
            :count="$acceptedRequests->count() ?: 0"
        />
        <x-status-box
            :bgClass="'bg-green-100 border-l-4 border-green-400 text-green-700'"
            :icon="'fa fa-check'"
            :infoBoxText="'Completed'"
            :count="$completedRequests->count() ?: 0"
        />
    </div>

    {{-- Tables --}}
    <div class="grid grid-cols-1 gap-6 mt-6">
        {{-- Assigned Requests --}}
        <x-instruction-request-table
            :instructionRequests="$myRequests"
            title="Assigned to {{ Auth::user()->display_name }}"
            headerClasses="bg-green-50 border-b border-green-300 text-green-800"
            :show-status="true"
        />

        {{-- Recently Received Requests --}}
        <x-instruction-request-table
            :instructionRequests="$tableRequests"
            title="Recently Received Requests"
            headerClasses="bg-blue-50 border-b border-blue-300 text-blue-800"
            :show-status="true"
        />
    </div>
@endsection
