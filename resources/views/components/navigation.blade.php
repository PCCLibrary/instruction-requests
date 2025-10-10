{{-- resources/views/components/navigation.blade.php --}}

{{-- Sidebar (Vertical Navigation) --}}
<aside class="bg-cyan-700 dark:bg-gray-800 w-64 min-h-screen flex flex-col fixed lg:relative z-30"
       :class="{'block': sidebarOpen, 'hidden lg:flex': !sidebarOpen}">

    {{-- Sidebar Header with Logo --}}
    <div class="px-6 py-4 border-b border-cyan-600 dark:border-gray-700">
        <img src="{{ \Illuminate\Support\Facades\Vite::asset('resources/png/library-logo.png') }}"
             alt="Library Logo"
             class="max-h-12 w-auto max-w-full object-contain">
    </div>

    {{-- Navigation Links --}}
    <nav class="flex-1 px-4 py-6 space-y-2">
        <a href="{{ route('dashboard') }}"
           class="flex items-center px-3 py-2 rounded-md text-sm font-medium transition-colors {{ Route::is('dashboard') ? 'text-white bg-cyan-800 dark:bg-gray-700' : 'text-gray-200 hover:bg-cyan-800 dark:hover:bg-gray-700' }}">
            <x-heroicon-o-chart-bar class="w-5 h-5 mr-3" />
            {{ __('Dashboard') }}
        </a>

        {{-- Separator --}}
        <div class="border-t border-cyan-600 dark:border-gray-700 my-2"></div>

        <a href="{{ route('instructionRequests.index') }}"
           class="flex items-center px-3 py-2 rounded-md text-sm font-medium transition-colors {{ Route::is('instructionRequests.*') ? 'text-white bg-cyan-800 dark:bg-gray-700' : 'text-gray-200 hover:bg-cyan-800 dark:hover:bg-gray-700' }}">
            <x-heroicon-o-document-text class="w-5 h-5 mr-3" />
            {{ __('Instruction Requests') }}
        </a>

        <a href="{{ route('statistics.index') }}"
           class="flex items-center px-3 py-2 rounded-md text-sm font-medium transition-colors {{ Route::is('statistics.*') ? 'text-white bg-cyan-800 dark:bg-gray-700' : 'text-gray-200 hover:bg-cyan-800 dark:hover:bg-gray-700' }}">
            <x-heroicon-o-chart-pie class="w-5 h-5 mr-3" />
            {{ __('Statistics') }}
        </a>

        {{-- Separator --}}
        <div class="border-t border-cyan-600 dark:border-gray-700 my-2"></div>

        <a href="{{ route('campuses.index') }}"
           class="flex items-center px-3 py-2 rounded-md text-sm font-medium transition-colors {{ Route::is('campuses.*') ? 'text-white bg-cyan-800 dark:bg-gray-700' : 'text-gray-200 hover:bg-cyan-800 dark:hover:bg-gray-700' }}">
            <x-heroicon-o-building-office-2 class="w-5 h-5 mr-3" />
            {{ __('Campuses') }}
        </a>

        <a href="{{ route('instructors.index') }}"
           class="flex items-center px-3 py-2 rounded-md text-sm font-medium transition-colors {{ Route::is('instructors.*') ? 'text-white bg-cyan-800 dark:bg-gray-700' : 'text-gray-200 hover:bg-cyan-800 dark:hover:bg-gray-700' }}">
            <x-heroicon-o-user-group class="w-5 h-5 mr-3" />
            {{ __('Instructors') }}
        </a>

        <a href="{{ route('users.index') }}"
           class="flex items-center px-3 py-2 rounded-md text-sm font-medium transition-colors {{ Route::is('users.*') ? 'text-white bg-cyan-800 dark:bg-gray-700' : 'text-gray-200 hover:bg-cyan-800 dark:hover:bg-gray-700' }}">
            <x-heroicon-o-user class="w-5 h-5 mr-3" />
            {{ __('Librarians') }}
        </a>
    </nav>
</aside>

{{-- Mobile Sidebar Overlay --}}
<div x-show="sidebarOpen"
     @click="sidebarOpen = false"
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-gray-600 bg-opacity-75 lg:hidden z-20"></div>
