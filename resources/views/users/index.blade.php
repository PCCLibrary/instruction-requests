@extends('layouts.app')

@section('header')
    <x-breadcrumbs :breadcrumbs="[
            ['label' => 'Manage Librarian Accounts'],
        ]" />
    <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
       Manage Librarian Accounts
    </h1>

    <p>Add and manage librarian accounts.</p>


@endsection

@section('content')

    @include('users.table')

@endsection

