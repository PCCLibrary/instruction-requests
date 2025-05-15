<!-- resources/views/campuses/create.blade.php -->
@extends('layouts.app')

@section('header')
    <x-breadcrumbs :breadcrumbs="[
            ['label' => 'Manage Campuses', 'route' => 'campuses.index'],
            ['label' => 'Create New Campus'] // No route for this one
        ]" />
    <h1 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:text-3xl sm:truncate">
        Create New Campus
    </h1>
@endsection

@section('content')

    <form action="{{ route('campuses.store') }}" method="POST" class="p-6">
        @csrf

        @include('campuses.partials.fields')

        <div class="gap-4 mt-6">
            <x-editor-actions
                route="{{ route('campuses.index') }}"
                :showBack="false"
            />
        </div>
    </form>

@endsection
