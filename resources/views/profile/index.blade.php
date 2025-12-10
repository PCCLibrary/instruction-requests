@extends('layouts.app')

@section('page-title', 'My Profile')
@section('page-description', 'Update your account information.')

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
