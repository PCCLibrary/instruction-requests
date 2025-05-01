@extends('layouts.app')

@section('header')
    <x-breadcrumbs :breadcrumbs="[
            ['label' => 'Manage Campuses'] // No route for this one
        ]" />
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Manage Campuses</h1>
            <p class="mt-1 text-gray-500">Manage campuses, add google calendar keys, and assign Librarians for notification.</p>
        </div>

        <a href="{{ route('campuses.create') }}"
           class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 rounded-md font-semibold text-xs text-white uppercase tracking-widest transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="h-4 w-4 mr-2"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 4v16m8-8H4" />
            </svg>
            Add Campus
        </a>

    </div>

@endsection

@section('content')

    @include('campuses.table')

@endsection
