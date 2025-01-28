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

        <div>
            <x-input-label for="name" :value="__('User Name')" />
            <x-text-input id="name"
                          name="name"
                          type="text"
                          class="mt-1 block w-full"
                          :value="old('name', $user->name)"
                          autofocus
                          autocomplete="username"
            />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="display_name" :value="__('Display Name')" />
            <x-text-input id="display_name"
                          name="display_name"
                          type="text"
                          class="mt-1 block w-full"
                          :value="old('display_name', $user->display_name ?? '')"
                          required
                          autocomplete="display_name" />
            <x-input-error class="mt-2" :messages="$errors->get('display_name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email"
                          name="email"
                          type="email"
                          class="mt-1 block w-full"
                          :value="old('email', $user->email ?? '')"
                          required autocomplete="email"
            />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error class="mt-2" :messages="$errors->get('password')" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
        </div>

        <div class="flex items-center gap-4">
            <x-button bgClass="bg-cyan-700 dark:bg-cyan-900" hoverClass="hover:bg-cyan-800 dark:hover:bg-cyan-900">
                {{ __('Save') }}
            </x-button>

            <a href="{{ route('users.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">
                {{ __('Cancel') }}
            </a>

            @if (session('status') === 'user-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-600 dark:text-green-400"
                >
                    {{ __('Saved.') }}
                </p>
            @endif
        </div>
    </form>

@endsection
