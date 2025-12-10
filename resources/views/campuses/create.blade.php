<!-- resources/views/campuses/create.blade.php -->
@extends('layouts.app')

@section('page-title', 'Create New Campus')

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
