<div class="space-y-4">
    {{-- Filter Panel --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
        <div class="bg-sky-500 dark:bg-sky-600 px-4 py-3 rounded-t-lg">
            <h3 class="text-lg font-medium text-white">Calendar Filters</h3>
        </div>

        <div class="p-4">
            <div class="space-y-4">
                {{-- Row 1: Librarian + Instruction Types --}}
                <div class="flex items-center justify-between">
                    {{-- Librarian Dropdown --}}
                    <div class="flex items-center gap-2">
                        <label for="librarian-select" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            Librarian:
                        </label>
                        <select
                            id="librarian-select"
                            wire:model.live="selectedLibrarianId"
                            class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">All Librarians</option>
                            @foreach($librarians as $librarian)
                                <option value="{{ $librarian->id }}">{{ $librarian->display_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Instruction Type Checkboxes --}}
                    <div class="flex items-center gap-4">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Instruction Type:</span>

                        <label class="inline-flex items-center cursor-pointer">
                            <input
                                type="checkbox"
                                wire:model.live="showOnCampus"
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:checked:bg-blue-600 dark:checked:border-blue-600"
                            >
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">In-Person</span>
                        </label>

                        <label class="inline-flex items-center cursor-pointer">
                            <input
                                type="checkbox"
                                wire:model.live="showRemote"
                                class="rounded border-gray-300 text-green-600 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:checked:bg-green-600 dark:checked:border-green-600"
                            >
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Remote</span>
                        </label>

                        <label class="inline-flex items-center cursor-pointer">
                            <input
                                type="checkbox"
                                wire:model.live="showAsynchronous"
                                class="rounded border-gray-300 text-amber-600 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-700 dark:checked:bg-amber-600 dark:checked:border-amber-600"
                            >
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Async</span>
                        </label>
                    </div>
                </div>

                {{-- Row 2: Status Filters --}}
                <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-600">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Request Status:
                    </span>

                    <div class="flex items-center gap-4">
                        <label class="inline-flex items-center cursor-pointer">
                            <input
                                type="checkbox"
                                wire:model.live="showReceived"
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:checked:bg-blue-600 dark:checked:border-blue-600"
                            >
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Received</span>
                        </label>

                        <label class="inline-flex items-center cursor-pointer">
                            <input
                                type="checkbox"
                                wire:model.live="showAssigned"
                                class="rounded border-gray-300 text-amber-600 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-700 dark:checked:bg-amber-600 dark:checked:border-amber-600"
                            >
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Assigned</span>
                        </label>

                        <label class="inline-flex items-center cursor-pointer">
                            <input
                                type="checkbox"
                                wire:model.live="showAccepted"
                                class="rounded border-gray-300 text-green-600 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:checked:bg-green-600 dark:checked:border-green-600"
                            >
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Accepted</span>
                        </label>

                        <label class="inline-flex items-center cursor-pointer">
                            <input
                                type="checkbox"
                                wire:model.live="showInProgress"
                                class="rounded border-gray-300 text-purple-600 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-700 dark:checked:bg-purple-600 dark:checked:border-purple-600"
                            >
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">In Progress</span>
                        </label>

                        <label class="inline-flex items-center cursor-pointer">
                            <input
                                type="checkbox"
                                wire:model.live="showRejected"
                                class="rounded border-gray-300 text-rose-600 focus:ring-rose-500 dark:border-gray-600 dark:bg-gray-700 dark:checked:bg-rose-600 dark:checked:border-rose-600"
                            >
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Rejected</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Calendar Grid and Event List --}}
    <div class="grid grid-cols-3 gap-6">
        {{-- Calendar Grid (2/3 width) --}}
        <div class="col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            {{-- Month Header --}}
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                    {{ \Carbon\Carbon::create($year, $month, 1)->format('F Y') }}
                </h2>
                <div class="flex gap-2">
                    <button
                        wire:click="previousMonth"
                        class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-gray-700 dark:text-gray-300"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <button
                        wire:click="goToCurrentMonth"
                        class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded"
                    >
                        Today
                    </button>
                    <button
                        wire:click="nextMonth"
                        class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-gray-700 dark:text-gray-300"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Day Headers --}}
            <div class="grid grid-cols-7 mb-2">
                @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $dayName)
                    <div class="text-center text-sm font-medium text-gray-600 dark:text-gray-400 py-2">
                        {{ $dayName }}
                    </div>
                @endforeach
            </div>

            {{-- Calendar Days --}}
            <div class="grid grid-cols-7 gap-0">
                @foreach($calendarGrid as $week)
                    @foreach($week as $day)
                        @if($day === null)
                            {{-- Empty cell --}}
                            <div class="h-24 border border-gray-200 dark:border-gray-700"></div>
                        @else
                            @php
                                $isToday = $todayDay === $day;
                                $isSelected = $selectedDay === $day;
                                $counts = $eventCounts[$day] ?? null;
                                $hasEvents = $counts && ($counts['on-campus'] > 0 || $counts['remote'] > 0 || $counts['asynchronous'] > 0);
                            @endphp
                            <div
                                wire:click="selectDay({{ $day }})"
                                class="h-24 border border-gray-200 dark:border-gray-700 p-2 cursor-pointer transition-colors
                                    {{ $isToday ? 'bg-yellow-50 dark:bg-yellow-900/20' : 'bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700' }}
                                    {{ $isSelected ? 'ring-2 ring-blue-500 ring-inset' : '' }}
                                "
                            >
                                <div class="font-medium text-sm mb-2 text-gray-900 dark:text-gray-100">{{ $day }}</div>
                                @if($hasEvents)
                                    <div class="flex flex-col gap-1 items-start">
                                        @if($counts['on-campus'] > 0)
                                            <span class="bg-blue-500 text-white text-xs px-2 py-0.5 rounded-full">
                                                {{ $counts['on-campus'] }}
                                            </span>
                                        @endif
                                        @if($counts['remote'] > 0)
                                            <span class="bg-green-500 text-white text-xs px-2 py-0.5 rounded-full">
                                                {{ $counts['remote'] }}
                                            </span>
                                        @endif
                                        @if($counts['asynchronous'] > 0)
                                            <span class="bg-amber-500 text-white text-xs px-2 py-0.5 rounded-full">
                                                {{ $counts['asynchronous'] }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endforeach
                @endforeach
            </div>
        </div>

        {{-- Event List Sidebar (1/3 width) --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            @if($selectedDay)
                @php
                    $selectedDate = \Carbon\Carbon::create($year, $month, $selectedDay);
                    $dayName = $selectedDate->format('l');
                @endphp
                <h3 class="text-lg font-semibold mb-1 text-gray-900 dark:text-gray-100">
                    {{ $dayName }}, {{ $selectedDate->format('F j') }}
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    {{ $selectedDayEvents->count() }} {{ $selectedDayEvents->count() === 1 ? 'event' : 'events' }}
                </p>

                <div class="space-y-2">
                    @forelse($selectedDayEvents as $event)
                        @php
                            $time = \Carbon\Carbon::parse($event->instruction_datetime)->format('g:ia');
                            // Border color by instruction type
                            $typeBorderColors = [
                                'on-campus' => 'border-l-blue-500',
                                'remote' => 'border-l-green-500',
                                'asynchronous' => 'border-l-amber-500',
                            ];
                            $borderClass = $typeBorderColors[$event->instruction_type] ?? 'border-l-gray-500';
                        @endphp
                        <div class="bg-white dark:bg-gray-800 border-l-4 {{ $borderClass }} border border-gray-200 dark:border-gray-700 rounded p-3 shadow-sm">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-gray-900 dark:text-gray-100 truncate">
                                        {{ $event->course_name }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">
                                        {{ $time }} • {{ $event->instructor_name }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-500 mt-1 capitalize">
                                        {{ str_replace('-', ' ', $event->instruction_type) }} • {{ ucfirst(str_replace('_', ' ', $event->status)) }}
                                        @if($event->librarian_name)
                                            • {{ $event->librarian_name }}
                                        @endif
                                    </div>
                                </div>
                                <a
                                    href="{{ url('/dashboard/instructionRequests/' . $event->id . '/edit') }}"
                                    class="inline-flex items-center p-2 text-xs bg-emerald-100 text-emerald-700 rounded-lg hover:bg-emerald-200 dark:bg-emerald-800 dark:text-emerald-300 dark:hover:bg-emerald-700"
                                    title="Edit Request"
                                >
                                    <x-heroicon-o-pencil-square class="w-4 h-4" />
                                </a>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 dark:text-gray-400 text-sm">No events scheduled</p>
                    @endforelse
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 text-sm">Select a day to view events</p>
            @endif
        </div>
    </div>
</div>
