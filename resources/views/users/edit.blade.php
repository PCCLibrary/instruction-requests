@extends('layouts.app')

@section('page-title', 'Edit Librarian')

@section('content')

    <form method="POST" action="{{ route('users.update', $user->id ?? '') }}" class="mt-6 space-y-6">
        @csrf
        @method('PATCH')
        @include('users.partials.fields')

        <div class="gap-4 mt-6">
            <x-editor-actions
                route="{{ route('users.index') }}"
                :showBack="false"
            />
        </div>

    </form>

    @include('users.partials.requests')

@endsection
