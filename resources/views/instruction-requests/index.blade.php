{{-- resources/views/instruction-requests/index.blade.php --}}
@extends('layouts.app')

@section('header')
        <x-breadcrumbs :breadcrumbs="[
            ['label' => 'Instruction Requests', 'route' => 'instructionRequests.index']
            ]" />
        <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
            Instruction Requests
        </h1>
        <p class="mt-2 text-base text-gray-600">
            Manage or create instruction requests.
        </p>
@endsection

@section('content')

{{--    @include('instruction-requests.partials.filters')--}}
    @include('instruction-requests.partials.table')

@endsection
