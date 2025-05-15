<!-- resources/views/instructors/edit.blade.php -->
@extends('layouts.app')

@section('header')
    <x-breadcrumbs :breadcrumbs="[
            ['label' => 'Manage Instructors', 'route' => 'instructors.index'],
            ['label' => 'Edit Instructor'] // No route for this one
        ]" />
    <h1 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:text-3xl sm:truncate">
        Editing Instructor: <span class="font-weight-lighter text-gray-500 dark:text-white sm:text-3xl sm:truncate">{{ $instructor->display_name }}</span>
    </h1>

@endsection

@section('content')
    <form action="{{ route('instructors.update', $instructor) }}" method="POST" class="p-6">
        @csrf
        @method('PUT')

        @include('instructors.partials.fields')

        <div class="gap-4 mt-6">
            <x-editor-actions
                route="{{ route('instructors.index') }}"
                :showBack="(bool)$instructor"
            />
        </div>
    </form>
    @include('instructors.partials.requests')
@endsection
