@props(['id', 'status', 'instructionType', 'isLocked' => false, 'lockerName' => null, 'editRoute' => 'instructionRequests.edit', 'routeKeyName' => 'instructionRequest', 'size' => 'w-4 h-4'])

<div class="flex space-x-2">
    {{-- Conditional Action Buttons Based on Status --}}
    @if($status === 'accepted')
        @if(in_array($instructionType, ['on-campus', 'remote']))
            {{-- Schedule Button for sync requests --}}
            <button
                @click="$dispatch('openScheduleModal', {{ $id }})"
                class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded dark:bg-indigo-700 dark:hover:bg-indigo-600"
                title="Schedule this instruction session">
                <x-heroicon-o-calendar-days class="{{ $size }} mr-1" />
                Schedule
            </button>
        @elseif($instructionType === 'asynchronous')
            {{-- Mark In Progress Button for async requests --}}
            <button
                wire:click="markInProgress({{ $id }})"
                class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-purple-600 hover:bg-purple-700 rounded dark:bg-purple-700 dark:hover:bg-purple-600"
                title="Mark this request as in progress">
                <x-heroicon-o-play class="{{ $size }} mr-1" />
                Mark In Progress
            </button>
        @endif
    @endif

    @if(in_array($status, ['scheduled', 'in_progress']))
        {{-- Mark Complete Button --}}
        <button
            wire:click="markComplete({{ $id }})"
            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-green-600 hover:bg-green-700 rounded dark:bg-green-700 dark:hover:bg-green-600"
            title="Mark this request as completed">
            <x-heroicon-o-check-circle class="{{ $size }} mr-1" />
            Mark Complete
        </button>
    @endif

    {{-- Edit Button (last in row) --}}
    @if($isLocked)
        <button type="button"
                disabled
                title="{{ $lockerName }} is currently editing this request"
                class="inline-flex items-center p-2 text-xs bg-gray-400 text-white rounded-lg cursor-not-allowed dark:bg-gray-600 dark:text-gray-200">
            <svg class="{{ $size }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </button>
    @else
        <a href="{{ route($editRoute, [$routeKeyName => $id]) }}"
           class="inline-flex items-center p-2 text-xs bg-emerald-100 text-emerald-700 rounded-lg hover:bg-emerald-200 dark:bg-emerald-800 dark:text-emerald-300 dark:hover:bg-emerald-700">
            <x-heroicon-o-pencil-square class="{{ $size }}" />
        </a>
    @endif
</div>
