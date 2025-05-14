{{-- resources/views/components/navigation.blade.php --}}
<nav class="bg-cyan-700 dark:bg-gray-800">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between h-16">
            {{-- Left side: Logo and Navigation --}}
            <div class="flex items-center">
                {{-- Brand/Logo --}}
                <div class="flex-shrink-0">
                    <a href="{{ route('dashboard') }}" class="text-xl font-bold text-white">
                        {{ __('Instruction Request Dashboard') }}
                    </a>
                </div>

                {{-- Desktop Navigation --}}
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="{{ route('campuses.index') }}"
                           class="hover:bg-cyan-800 dark:hover:bg-gray-700 px-3 py-2 rounded-md text-sm font-medium {{ Route::is('campuses.*') ? 'text-white' : 'text-gray-200' }}">
                            {{ __('Campuses') }}
                        </a>

                        <a href="{{ route('instructors.index') }}"
                           class="hover:bg-cyan-800 dark:hover:bg-gray-700 px-3 py-2 rounded-md text-sm font-medium {{ Route::is('instructors.*') ? 'text-white' : 'text-gray-200' }}">
                            {{ __('Instructors') }}
                        </a>

                        <a href="{{ route('users.index') }}"
                           class="hover:bg-cyan-800 dark:hover:bg-gray-700 px-3 py-2 rounded-md text-sm font-medium {{ Route::is('users.*') ? 'text-white' : 'text-gray-200' }}">
                            {{ __('Librarians') }}
                        </a>

                        <a href="{{ route('instructionRequests.index') }}"
                           class="hover:bg-cyan-800 dark:hover:bg-gray-700 px-3 py-2 rounded-md text-sm font-medium {{ Route::is('instructionRequests.*') ? 'text-white' : 'text-gray-200' }}">
                            {{ __('Instruction Requests') }}
                        </a>
                    </div>
                </div>
            </div>

            {{-- Right side: Dark Mode Toggle and User Dropdown --}}
            <div class="hidden md:block">
                <div class="ml-4 flex items-center md:ml-6">
                    {{-- Dark Mode Toggle --}}
                    <button
                        @click="darkMode = !darkMode"
                        class="text-gray-200 hover:text-white px-3 py-2 rounded-md">
                        <svg x-show="!darkMode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                        <svg x-show="darkMode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </button>

                    {{-- User Dropdown --}}
                    <div x-data="{ open: false }" @click.away="open = false" class="ml-3 relative">
                        <div>
                            <button @click="open = !open"
                                    class="flex items-center text-white hover:bg-cyan-800 dark:hover:bg-gray-700 px-3 py-2 rounded-md text-sm font-medium focus:outline-none"
                                    id="user-menu-button"
                                    aria-expanded="false"
                                    aria-haspopup="true">
                                <x-heroicon-o-user class="h-5 w-5 mr-2" />
                                {{ Auth::user()->display_name }}
                                <x-heroicon-s-chevron-down class="ml-2 h-4 w-4" />
                            </button>
                        </div>

                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-cyan-800 dark:bg-gray-700"
                             role="menu"
                             aria-orientation="vertical"
                             aria-labelledby="user-menu-button"
                             tabindex="-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-200 hover:bg-cyan-700 dark:hover:bg-gray-600 hover:text-white"
                                        role="menuitem">
                                    {{ __('Logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Mobile menu button --}}
            <div class="-mr-2 flex md:hidden">
                {{-- Dark Mode Toggle (Mobile) --}}
                <button
                    @click="darkMode = !darkMode"
                    class="text-gray-200 hover:text-white p-2 mr-2">
                    <svg x-show="!darkMode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <svg x-show="darkMode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </button>

                <button type="button"
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        class="bg-cyan-600 dark:bg-gray-700 inline-flex items-center justify-center p-2 rounded-md text-white hover:bg-cyan-500 dark:hover:bg-gray-600 focus:outline-none">
                    <span class="sr-only">Open main menu</span>
                    <svg class="h-6 w-6"
                         :class="{'hidden': mobileMenuOpen, 'block': !mobileMenuOpen }"
                         xmlns="http://www.w3.org/2000/svg"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg class="hidden h-6 w-6"
                         :class="{'block': mobileMenuOpen, 'hidden': !mobileMenuOpen }"
                         xmlns="http://www.w3.org/2000/svg"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div class="md:hidden"
         x-show="mobileMenuOpen"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95">
        <div class="px-2 pt-2 pb-3 space-y-1 container mx-auto">
            <a href="{{ route('campuses.index') }}"
               class="hover:bg-cyan-800 dark:hover:bg-gray-700 block px-3 py-2 rounded-md text-base font-medium {{ Route::is('campuses.*') ? 'text-white' : 'text-gray-200' }}">
                {{ __('Campuses') }}
            </a>

            <a href="{{ route('instructors.index') }}"
               class="hover:bg-cyan-800 dark:hover:bg-gray-700 block px-3 py-2 rounded-md text-base font-medium {{ Route::is('instructors.*') ? 'text-white' : 'text-gray-200' }}">
                {{ __('Instructors') }}
            </a>

            <a href="{{ route('users.index') }}"
               class="hover:bg-cyan-800 dark:hover:bg-gray-700 block px-3 py-2 rounded-md text-base font-medium {{ Route::is('users.*') ? 'text-white' : 'text-gray-200' }}">
                {{ __('Librarians') }}
            </a>

            <a href="{{ route('instructionRequests.index') }}"
               class="hover:bg-cyan-800 dark:hover:bg-gray-700 block px-3 py-2 rounded-md text-base font-medium {{ Route::is('instructionRequests.*') ? 'text-white' : 'text-gray-200' }}">
                {{ __('Instruction Requests') }}
            </a>
        </div>

        {{-- Mobile menu profile section --}}
        <div class="pt-4 pb-3 border-t border-cyan-800 dark:border-gray-700">
            <div class="container mx-auto px-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-user class="h-8 w-8 text-white" />
                    </div>
                    <div class="ml-3">
                        <div class="text-base font-medium leading-none text-white">{{ Auth::user()->name }}</div>
                        <div class="text-sm font-medium leading-none text-cyan-200 dark:text-gray-300 mt-1">{{ Auth::user()->email }}</div>
                    </div>
                </div>
                <div class="mt-3 space-y-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-gray-200 hover:bg-cyan-800 dark:hover:bg-gray-700 hover:text-white">
                            {{ __('Logout') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>
