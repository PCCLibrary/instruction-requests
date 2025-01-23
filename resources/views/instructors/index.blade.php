@extends('layouts.app')

@section('header')
    <x-breadcrumbs :breadcrumbs="[
            ['label' => 'Manage Instructors'] // No route for this one
        ]" />
    <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
        Manage Instructors
    </h1>
    <p>Edit and manage instructors.</p>

@endsection
@section('content')

 @include('instructors.table')

@endsection

