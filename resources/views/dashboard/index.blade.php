@extends('layouts.app')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">

        {{-- Left Column: Tables --}}
        <div class="space-y-6">
            {{-- Assigned Requests --}}
            <x-instruction-request-table
                :instructionRequests="$myRequests"
                title="Assigned to {{ Auth::user()->display_name }}"
                headerClasses="bg-olive"
                :show-status="true"
            />

            {{-- Recently Received Requests --}}
            <x-instruction-request-table
                :instructionRequests="$tableRequests"
                title="Recently Received Requests"
            />
        </div>

        {{-- Right Column: Status Boxes --}}
        <div class="space-y-6">
            {{-- Status Boxes --}}
            @include('components.status_box', [
                'bgClass' => 'bg-warning',
                'icon' => 'fa fa-file',
                'infoBoxText' => 'Received',
                'count' => $pendingRequests->count() ?: 0
            ])
            @include('components.status_box', [
                'bgClass' => 'bg-info',
                'icon' => 'fa fa-user',
                'infoBoxText' => 'Assigned',
                'count' => $inProgressRequests->count() ?: 0
            ])
            @include('components.status_box', [
                'bgClass' => 'bg-teal',
                'icon' => 'fa fa-star',
                'infoBoxText' => 'Accepted',
                'count' => $acceptedRequests->count() ?: 0
            ])
            @include('components.status_box', [
                'bgClass' => 'bg-success',
                'icon' => 'fa fa-check',
                'infoBoxText' => 'Completed',
                'count' => $completedRequests->count() ?: 0
            ])
            @include('components.status_box', [
                'bgClass' => 'bg-grey',
                'icon' => 'fa fa-graduation-cap',
                'infoBoxText' => 'Instructors',
                'count' => $instructorCount
            ])
            @include('components.status_box', [
                'bgClass' => 'bg-dark',
                'icon' => 'fa fa-clock',
                'infoBoxText' => 'Instruction Hours',
                'count' => $totalInstructionHours
            ])
        </div>

    </div>
@endsection
