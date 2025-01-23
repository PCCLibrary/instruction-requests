@extends('layouts.app')

@section('header')
    <x-breadcrumbs :breadcrumbs="[
            ['label' => 'My Profile'] // No route for this one
        ]" />
    <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
        My Profile
    </h1>
    <p>Update your account information. </p>

@endsection

@section('content')

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <x-card class="mb-4">
            @include('profile.partials.edit-profile-form')
        </x-card>
        <x-card class="mb-4">
            @include('profile.partials.update-password-form')
        </x-card>
    </div>

@endsection
