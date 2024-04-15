@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-4">

        <div class="row">

        <div class="col-6">

            <x-instruction-request-table :instructionRequests="$myRequests"
                                         title="Assigned to {{ Auth::user()->display_name }}"
                                         headerClasses="bg-olive"
                                         :show-status='true'

            />

            <x-instruction-request-table :instructionRequests="$tableRequests"
                                          title="Recently Received Requests"
            />

        </div>

            <div class="col-6">

                <div class="col-12">
                    @include('components.status_box', [
                    'bgClass' => 'bg-warning',
                    'icon' => 'fa fa-file',
                    'infoBoxText' => 'Received',
                    'count' => $pendingRequests->count() ? : 0
                    ])
                </div>

                <div class="col-12">
                    @include('components.status_box', [
                    'bgClass' => 'bg-info',
                    'icon' => 'fa fa-user',
                    'infoBoxText' => 'Assigned',
                    'count' => $inProgressRequests->count() ? : 0
                    ])
                </div>

                <div class="col-12">
                    @include('components.status_box', [
                    'bgClass' => 'bg-teal',
                    'icon' => 'fa fa-star',
                    'infoBoxText' => 'Accepted',
                    'count' => $acceptedRequests->count() ? $inProgressRequests->count() : 0
                    ])
                </div>

                <div class="col-12">
                    @include('components.status_box', [
                    'bgClass' => 'bg-success',
                    'icon' => 'fa fa-check',
                    'infoBoxText' => 'Completed',
                    'count' => $completedRequests->count() ? $completedRequests->count() : 0
                    ])
                </div>

                <div class="col-12">
                    @include('components.status_box', [
                    'bgClass' => 'bg-grey',
                    'icon' => 'fa fa-graduation-cap',
                    'infoBoxText' => 'Instructors',
                    'count' => $instructorCount
                    ])
                </div>

                <div class="col-12">
                    @include('components.status_box', [
                    'bgClass' => 'bg-dark',
                    'icon' => 'fa fa-clock',
                    'infoBoxText' => 'Instruction Hours',
                    'count' => $totalInstructionHours
                    ])
                </div>

            </div>

        </div>

    </div>
@endsection
