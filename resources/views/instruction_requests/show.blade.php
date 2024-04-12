@extends('layouts.app')

@section('content')
    <div class="content px-3">

        @include('flash::message')

        <div class="clearfix"></div>

        @include('adminlte-templates::common.errors')

        <div class="row mt-4">

            <div class="col-md-9">

                @include('instruction_requests.resources.views.instruction_requests.admin_view')

            </div>

            <div class="col-md-3">
                <x-card title="View Instruction Request"
                        headerclass="bg-green">
                    @include('instruction_requests.admin.view')
                </x-card>

            </div>

        </div>

@endsection
