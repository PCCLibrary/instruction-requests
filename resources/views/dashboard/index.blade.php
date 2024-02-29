@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="row">

            <div class="col-6">
                @include('components.status_box', [
                'bgClass' => 'bg-success',
                'icon' => 'fa fa-file',
                'infoBoxText' => 'New Requests',
                'count' => '200'
                ])
            </div>

            <div class="col-6">
                @include('components.status_box', [
                'bgClass' => 'bg-info',
                'icon' => 'fa fa-check',
                'infoBoxText' => 'Completed',
                'count' => '20'
                ])
            </div>

        </div>

        <div class="row">

            <div class="col-6">
                @include('components.status_box', [
                'bgClass' => 'bg-warning',
                'icon' => 'fa fa-graduation-cap',
                'infoBoxText' => 'Instructors',
                'count' => '53'
                ])
            </div>

            <div class="col-6">
                @include('components.status_box', [
                'bgClass' => 'bg-teal',
                'icon' => 'fa fa-clock',
                'infoBoxText' => 'Instruction Hours',
                'count' => '37'
                ])
            </div>

        </div>


    </div>
@endsection
