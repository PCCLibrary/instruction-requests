@props([
    'instructionRequests',
    'title' => 'Instruction Requests',
    'showInstructor' => true,
    'showClass' => true,
    'showDate' => true,
    'showStatus' => false,
    'showCampus' => false,
    'showLibrarian' => true,
    'compact' => false,
    'limit' => null,
    'headerBgColor' => 'bg-sky-500',
    'showFooter' => false
])

<div class="w-full rounded-md overflow-hidden border border-gray-200 dark:border-gray-700">
    {{-- Header --}}
    @if($title)
        <div class="flex items-center justify-between {{ $headerBgColor }} px-4 py-2">
            <h3 class="text-sm font-medium text-white">{{ $title }}</h3>
            @if($limit && $instructionRequests->count() >= $limit)
                <a href="{{ route('instructionRequests.index') }}" class="text-xs text-white hover:text-blue-100">
                    View All
                </a>
            @endif
        </div>
    @endif

    {{-- Table --}}
    <div class="w-full overflow-x-auto">
        <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
                @if($showDate)
                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 tracking-wider">
                        {{ $showStatus ? 'Received' : 'Date' }}
                    </th>
                @endif

                @if($showCampus)
                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 tracking-wider">
                        Campus
                    </th>
                @endif

                @if($showClass)
                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 tracking-wider">
                        Class
                    </th>
                @endif

                @if($showInstructor)
                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 tracking-wider">
                        Instructor
                    </th>
                @endif

                @if($showLibrarian)
                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 tracking-wider">
                        Librarian
                    </th>
                @endif

                @if($showStatus)
                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 tracking-wider">
                        Status
                    </th>
                @endif

                <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 tracking-wider">
                    Actions
                </th>
            </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
            @if($instructionRequests->count() > 0)
                @foreach($instructionRequests as $request)
                    <tr class="even:bg-gray-50 dark:even:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        @if($showDate)
                            <td class="px-3 py-2 {{ $compact ? 'text-sm' : 'text-base' }} text-gray-500 dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($request->created_at)
                                    ->timezone('America/Los_Angeles')
                                    ->format($compact ? 'm/d/y' : 'm/d/y g:i A') }}
                            </td>
                        @endif

                        @if($showCampus)
                            <td class="px-3 py-2 {{ $compact ? 'text-sm' : 'text-base' }} text-gray-900 dark:text-gray-100">
                                {{ $request->campus->name }}
                            </td>
                        @endif

                        @if($showClass)
                            <td class="px-3 py-2 {{ $compact ? 'text-sm' : 'text-base' }} text-gray-900 dark:text-gray-100">
                                <div class="flex flex-col">
                                    <span>{{ $request->classes->course_name }}</span>
                                </div>
                            </td>
                        @endif

                        @if($showInstructor)
                            <td class="px-3 py-2 {{ $compact ? 'text-sm' : 'text-base' }} text-gray-900 dark:text-gray-100">
                                {{ $request->instructor->name ?? 'Unknown Instructor' }}
                            </td>
                        @endif

                        @if($showLibrarian)
                            <td class="px-3 py-2 {{ $compact ? 'text-sm' : 'text-base' }} text-gray-900 dark:text-gray-100">
                                {{ $request->detail->assignedLibrarian->display_name ?? 'Unassigned' }}
                            </td>
                        @endif

                        @if($showStatus)
                            <td class="px-3 py-2">
                                <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5
                                    {{ match($request->status) {
                                        'received' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                        'assigned' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                        'accepted' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                        'copied' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
                                        default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
                                    } }}">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </td>
                        @endif

                        <td class="px-3 py-2 text-right">
                            <a href="{{ route('instructionRequests.edit', $request->id) }}"
                               class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                <x-heroicon-o-pencil-square class="ml-1 w-4 h-4" />
                            </a>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="{{
                        (int)$showDate +
                        (int)$showCampus +
                        (int)$showClass +
                        (int)$showInstructor +
                        (int)$showLibrarian +
                        (int)$showStatus +
                        1
                    }}" class="px-3 py-4 text-sm text-center text-gray-500 dark:text-gray-400">
                        No requests found.
                    </td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>

    {{-- Footer --}}
    @if($showFooter)
        <div class="bg-white dark:bg-gray-900 px-4 py-2 border-t border-gray-200 dark:border-gray-700">
            <div class="flex justify-end">
                <a href="{{ route('instructionRequests.create') }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-700 dark:hover:bg-emerald-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest transition-colors mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-4 w-4 mr-2"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M12 4v16m8-8H4" />
                    </svg>
                    Create New Request
                </a>
                <a href="{{ route('instructionRequests.index') }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest transition-colors">
                    <span>View all requests</span>
                    <x-heroicon-o-arrow-right class="w-4 h-4" />
                </a>
            </div>
        </div>
    @endif
</div>
