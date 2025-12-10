<!-- resources/views/instructors/edit.blade.php -->
@extends('layouts.app')

@section('page-title')
    Editing Instructor: <span class="font-weight-lighter text-gray-500 dark:text-gray-300">{{ $instructor->display_name }}</span>
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
