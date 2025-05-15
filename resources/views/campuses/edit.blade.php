<!-- resources/views/campuses/edit.blade.php -->
@extends('layouts.app')

@section('header')
    <x-breadcrumbs :breadcrumbs="[
            ['label' => 'Manage Campuses', 'route' => 'campuses.index'],
            ['label' => 'Edit Campus'] // No route for this one
        ]" />
    <h1 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:text-3xl sm:truncate">
        Editing Campus: <span class="font-weight-lighter text-gray-500 dark:text-gray-300">{{ $campus->name }}</span>
    </h1>

@endsection

@section('content')

    <form action="{{ route('campuses.update', $campus) }}" method="POST" class="p-4 bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
        @csrf
        @method('PUT')

        @include('campuses.partials.fields')

        <div class="gap-4 mt-6">
            <x-editor-actions
                route="{{ route('campuses.index') }}"
                :showBack="(bool)$campus"
            />
        </div>
    </form>

@endsection
