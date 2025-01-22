{{-- resources/views/components/navigation.blade.php --}}
<nav class="bg-cyan-700">
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
                           class="text-white hover:bg-cyan-600 px-3 py-2 rounded-md text-sm font-medium">
                            {{ __('Campuses') }}
                        </a>

                        <a href="{{ route('instructors.index') }}"
                           class="text-white hover:bg-cyan-600 px-3 py-2 rounded-md text-sm font-medium">
                            {{ __('Instructors') }}
                        </a>

                        <a href="{{ route('classes.index') }}"
                           class="text-white hover:bg-cyan-600 px-3 py-2 rounded-md text-sm font-medium">
                            {{ __('Classes') }}
                        </a>

                        <a href="{{ route('users.index') }}"
                           class="text-white hover:bg-cyan-600 px-3 py-2 rounded-md text-sm font-medium">
                            {{ __('Librarians') }}
                        </a>

                        <a href="{{ route('instructionRequests.index') }}"
                           class="text-white hover:bg-cyan-600 px-3 py-2 rounded-md text-sm font-medium">
                            {{ __('Instruction Requests') }}
                        </a>

                        @can('viewAny', \App\Models\User::class)
                            <a href="{{ route('users.index') }}"
                               class="text-white hover:bg-cyan-600 px-3 py-2 rounded-md text-sm font-medium">
                                {{ __('Users') }}
                            </a>
                        @endcan
                    </div>
                </div>
            </div>

            {{-- Right side: User Dropdown --}}
            <div class="hidden md:block">
                <div class="ml-4 flex items-center md:ml-6">
                    {{-- User Dropdown --}}
                    <div x-data="{ open: false }" @click.away="open = false" class="ml-3 relative">
                        <div>
                            <button @click="open = !open"
                                    class="flex items-center text-white hover:bg-cyan-600 px-3 py-2 rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-cyan-700 focus:ring-white"
                                    id="user-menu-button"
                                    aria-expanded="false"
                                    aria-haspopup="true">
                                <x-heroicon-o-user class="h-5 w-5 mr-2" />
                                {{ Auth::user()->name }}
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
                             class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                             role="menu"
                             aria-orientation="vertical"
                             aria-labelledby="user-menu-button"
                             tabindex="-1">
                            <a href="{{ route('profile.index') }}"
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                               role="menuitem">
                                {{ __('Profile') }}
                            </a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
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
                <button type="button"
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        class="bg-cyan-600 inline-flex items-center justify-center p-2 rounded-md text-white hover:bg-cyan-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-cyan-700 focus:ring-white">
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
               class="text-white hover:bg-cyan-600 block px-3 py-2 rounded-md text-base font-medium">
                {{ __('Campuses') }}
            </a>

            <a href="{{ route('instructors.index') }}"
               class="text-white hover:bg-cyan-600 block px-3 py-2 rounded-md text-base font-medium">
                {{ __('Instructors') }}
            </a>

            <a href="{{ route('classes.index') }}"
               class="text-white hover:bg-cyan-600 block px-3 py-2 rounded-md text-base font-medium">
                {{ __('Classes') }}
            </a>

            <a href="{{ route('users.index') }}"
               class="text-white hover:bg-cyan-600 block px-3 py-2 rounded-md text-base font-medium">
                {{ __('Librarians') }}
            </a>

            <a href="{{ route('instructionRequests.index') }}"
               class="text-white hover:bg-cyan-600 block px-3 py-2 rounded-md text-base font-medium">
                {{ __('Instruction Requests') }}
            </a>

            @can('viewAny', \App\Models\User::class)
                <a href="{{ route('users.index') }}"
                   class="text-white hover:bg-cyan-600 block px-3 py-2 rounded-md text-base font-medium">
                    {{ __('Users') }}
                </a>
            @endcan
        </div>

        {{-- Mobile menu profile section --}}
        <div class="pt-4 pb-3 border-t border-cyan-800">
            <div class="container mx-auto px-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-user class="h-8 w-8 text-white" />
                    </div>
                    <div class="ml-3">
                        <div class="text-base font-medium leading-none text-white">{{ Auth::user()->name }}</div>
                        <div class="text-sm font-medium leading-none text-cyan-200 mt-1">{{ Auth::user()->email }}</div>
                    </div>
                </div>
                <div class="mt-3 space-y-1">
                    <a href="{{ route('profile.index') }}"
                       class="block px-3 py-2 rounded-md text-base font-medium text-white hover:bg-cyan-600">
                        {{ __('Profile') }}
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-white hover:bg-cyan-600">
                            {{ __('Logout') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>
