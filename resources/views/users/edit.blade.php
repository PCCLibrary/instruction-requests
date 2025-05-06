@extends('layouts.app')

@section('header')
    <x-breadcrumbs :breadcrumbs="[
            ['label' => 'Manage Librarian Accounts', 'route' => 'users.index'],
            ['label' => 'Edit User'] // No route for this one
        ]" />
    <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
        Edit User
    </h1>

@endsection

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

@endsection
