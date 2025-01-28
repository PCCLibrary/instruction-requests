@extends('layouts.app')

@section('header')
    <x-breadcrumbs :breadcrumbs="[
            ['label' => 'Manage Librarian Accounts'],
        ]" />
    <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
       Manage Librarian Accounts
    </h1>

    <p>Add and manage librarian accounts.</p>
    <a href="{{ route('users.create') }}" class="inline-flex items-center px-4 py-2 mt-4 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
        Add Librarian
    </a>
@endsection

@section('content')

    @include('users.table')

@endsection

