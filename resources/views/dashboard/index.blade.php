@extends('layouts.app')


@section('content')
    <div class="container-fluid mt-4">

        <div class="row">

        <div class="col-6">

            <div class="col-12">

                <x-instruction-request-table :instructionRequests="$tableRequests" />

            </div>
        </div>

            <div class="col-6">

                <div class="col-12">
                    @include('components.status_box', [
                    'bgClass' => 'bg-success',
                    'icon' => 'fa fa-file',
                    'infoBoxText' => 'Pending Requests',
                    'count' => $pendingRequests->count() ? $pendingRequests->count() : 0
                    ])
                </div>

                <div class="col-12">
                    @include('components.status_box', [
                    'bgClass' => 'bg-warning',
                    'icon' => 'fa fa-wrench',
                    'infoBoxText' => 'In Progress',
                    'count' => $inProgressRequests->count() ? $inProgressRequests->count() : 0
                    ])
                </div>

                <div class="col-12">
                    @include('components.status_box', [
                    'bgClass' => 'bg-info',
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
