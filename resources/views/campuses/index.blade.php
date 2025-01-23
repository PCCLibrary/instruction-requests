@extends('layouts.app')

@section('header')
    <x-breadcrumbs :breadcrumbs="[
            ['label' => 'Manage Campuses'] // No route for this one
        ]" />
    <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
        Manage Campuses
    </h1>
    <p>Edit and manage campuses, and assign librarians to notify when new requests arrive.</p>

@endsection

@section('content')

    @include('campuses.table')

@endsection
