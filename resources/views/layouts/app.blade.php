<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

{{--    @commentsStyles--}}
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">
{{-- Include Navigation --}}
<div x-data="{ mobileMenuOpen: false }">
    @include('components.navigation')
</div>
<!-- Page Heading -->
@if (View::hasSection('header'))
    <header class="py-6 bg-white dark:bg-gray-800 shadow">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            @yield('header')
        </div>
    </header>
@endif

<!-- Page Content -->
<main class="py-0">
    <x-alerts/>
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        @yield('content')
    </div>
</main>
<x-footer/>
{{--@commentsScripts--}}
@livewireScripts
</body>
</html>
