@extends('layouts.app')

@section('header')
    <x-breadcrumbs :breadcrumbs="[
            ['label' => 'Manage Instructors'] // No route for this one
        ]" />
    <h1 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:text-3xl sm:truncate">
        Manage Instructors
    </h1>
    <p class="text-gray-600 dark:text-gray-300">Edit and manage instructors. New instructors can be added when filling in a Instruction Request Form.</p>

@endsection
@section('content')

 @include('instructors.table')

@endsection
