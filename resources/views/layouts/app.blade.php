<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', value => localStorage.setItem('darkMode', value))" x-bind:class="{'dark': darkMode}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="index-route" content="{{ route('instructionRequests.index') }}">

    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Initialize dark mode from local storage -->
    <script>
        if (localStorage.getItem('darkMode') === 'true' ||
            (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
            localStorage.setItem('darkMode', 'true');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    @commentsStyles
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">

<!-- L-Shaped Layout Container -->
<div class="min-h-screen flex" x-data="{ sidebarOpen: false }">
    {{-- Include Navigation (Sidebar + Top Header) --}}
    @include('components.navigation')

    {{-- Main Content Area --}}
    <div class="flex-1 flex flex-col">
        {{-- Top Header (Mobile) --}}
        <header class="lg:hidden bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 flex items-center justify-between">
                {{-- Mobile menu button --}}
                <button @click="sidebarOpen = !sidebarOpen"
                        class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none">
                    <span class="sr-only">Open sidebar</span>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                {{-- Right side: User controls --}}
                <div class="flex items-center space-x-4">
                    {{-- Role Badge --}}
                    <x-user-role-badge :user="Auth::user()" />

                    {{-- User Dropdown --}}
                    <div x-data="{ open: false }" @click.away="open = false" class="relative">
                        <button @click="open = !open"
                                class="flex items-center text-gray-700 dark:text-gray-200 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium focus:outline-none"
                                id="user-menu-button-mobile"
                                aria-expanded="false"
                                aria-haspopup="true">
                            <x-heroicon-o-user class="h-5 w-5 mr-2" />
                            {{ Auth::user()->display_name }}
                            <x-heroicon-s-chevron-down class="ml-2 h-4 w-4" />
                        </button>

                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5 z-50"
                             role="menu"
                             aria-orientation="vertical"
                             aria-labelledby="user-menu-button-mobile"
                             tabindex="-1">
                            @if(Auth::user()->is_admin)
                                <a href="{{ route('admin.index') }}"
                                   class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600"
                                   role="menuitem">
                                    {{ __('Admin Panel') }}
                                </a>
                            @endif
                            <a href="{{ route('users.edit', Auth::user()->id) }}"
                               class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600"
                               role="menuitem">
                                {{ __('Edit Profile') }}
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600"
                                        role="menuitem">
                                    {{ __('Logout') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Dark Mode Toggle --}}
                    <button
                        @click="darkMode = !darkMode"
                        class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none">
                        <svg x-show="!darkMode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                        <svg x-show="darkMode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </button>
                </div>
            </div>
        </header>

        {{-- Top Header (Desktop Only) --}}
        <header class="hidden lg:block bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 flex items-center justify-end">
                {{-- Right side: User controls --}}
                <div class="flex items-center space-x-4">
                    {{-- Role Badge --}}
                    <x-user-role-badge :user="Auth::user()" />

                    {{-- User Dropdown --}}
                    <div x-data="{ open: false }" @click.away="open = false" class="relative">
                        <button @click="open = !open"
                                class="flex items-center text-gray-700 dark:text-gray-200 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium focus:outline-none"
                                id="user-menu-button"
                                aria-expanded="false"
                                aria-haspopup="true">
                            <x-heroicon-o-user class="h-5 w-5 mr-2" />
                            {{ Auth::user()->display_name }}
                            <x-heroicon-s-chevron-down class="ml-2 h-4 w-4" />
                        </button>

                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5 z-50"
                             role="menu"
                             aria-orientation="vertical"
                             aria-labelledby="user-menu-button"
                             tabindex="-1">
                            @if(Auth::user()->is_admin)
                                <a href="{{ route('admin.index') }}"
                                   class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600"
                                   role="menuitem">
                                    {{ __('Admin Panel') }}
                                </a>
                            @endif
                            <a href="{{ route('users.edit', Auth::user()->id) }}"
                               class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600"
                               role="menuitem">
                                {{ __('Edit Profile') }}
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600"
                                        role="menuitem">
                                    {{ __('Logout') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Dark Mode Toggle --}}
                    <button
                        @click="darkMode = !darkMode"
                        class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none">
                        <svg x-show="!darkMode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                        <svg x-show="darkMode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </button>
                </div>
            </div>
        </header>

        <!-- Page Heading -->
        @if (View::hasSection('header'))
            <header class="py-6 bg-white dark:bg-gray-800 shadow">
                <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                    @yield('header')
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main class="py-0 flex-1">
            <x-alerts/>
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 xl:px-8 2xl:px-8 3xl:px-8">
                @yield('content')
            </div>
        </main>

        <x-footer/>
    </div>
</div>

<x-toaster-hub />

@commentsScripts
@livewireScripts
@stack('scripts')

</body>
</html>
