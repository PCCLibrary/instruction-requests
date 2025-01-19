{{-- resources/views/components/navigation.blade.php --}}
<nav class="bg-white border-b border-gray-200 px-4 py-2 flex items-center justify-between">
    {{-- Left side: "Dashboard" branding/link --}}
    <div class="text-xl font-bold">
        <a href="{{ route('dashboard') }}" class="hover:text-gray-600">
            {{ __('Instruction Request Dashboard') }}
        </a>
    </div>

    {{-- Right side: horizontal links --}}
    <ul class="flex items-center gap-6">
        {{-- Link to Campuses --}}
        <li>
            <a href="{{ route('campuses.index') }}"
               class="text-gray-700 hover:underline"
            >
                {{ __('Campuses') }}
            </a>
        </li>

        {{-- Link to Instructors --}}
        <li>
            <a href="{{ route('instructors.index') }}"
               class="text-gray-700 hover:underline"
            >
                {{ __('Instructors') }}
            </a>
        </li>

        {{-- Link to Instructors --}}
        <li>
            <a href="{{ route('classes.index') }}"
               class="text-gray-700 hover:underline"
            >
                {{ __('Classes') }}
            </a>
        </li>

        {{-- Link to Instructors --}}
        <li>
            <a href="{{ route('users.index') }}"
               class="text-gray-700 hover:underline"
            >
                {{ __('Librarians') }}
            </a>
        </li>

        {{-- Link to Instruction Requests --}}
        <li>
            <a href="{{ route('instructionRequests.index') }}"
               class="text-gray-700 hover:underline"
            >
                {{ __('Instruction Requests') }}
            </a>
        </li>

        {{-- Link to User Management (optional, if admin role) --}}
        @can('viewAny', \App\Models\User::class)
            <li>
                <a href="{{ route('users.index') }}"
                   class="text-gray-700 hover:underline"
                >
                    {{ __('Users') }}
                </a>
            </li>
        @endcan

        {{-- Link to Profile --}}
        <li>
            <a href="{{ route('profile.index') }}"
               class="text-gray-700 hover:underline"
            >
                {{ __('Profile') }}
            </a>
        </li>

        {{-- Logout form --}}
        <li>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-gray-700 hover:underline">
                    {{ __('Logout') }}
                </button>
            </form>
        </li>
    </ul>
</nav>
