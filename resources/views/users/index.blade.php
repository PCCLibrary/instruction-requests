@extends('layouts.app')

@section('header')
    <x-breadcrumbs :breadcrumbs="[
            ['label' => 'Manage Librarian Accounts'],
        ]" />

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Manage Librarian Accounts</h1>
            <p class="mt-1 text-gray-500">Add and manage librarian accounts.</p>
        </div>

        <a href="{{ route('users.create') }}"
           class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Add Librarian
        </a>
    </div>



@endsection

@section('content')

    @include('users.table')

@endsection

