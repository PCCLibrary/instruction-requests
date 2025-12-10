@extends('layouts.app')

@section('page-title', 'Add Librarian')

@section('content')

    <form method="POST" action="{{ route('users.store') }}" class="mt-6 space-y-6">
        @csrf

        @include('users.partials.fields')

        <div class="gap-4 mt-6">
            <x-editor-actions
                route="{{ route('users.index') }}"
                :showBack="false"
            />
        </div>

    </form>

@endsection
