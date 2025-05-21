@props(['route', 'showBack' => false, 'unlockRequest' => false, 'confirmMessage' => 'Are you sure you want to delete this item?'])

<div class="flex space-x-2">
    @if(isset($isLocked) && $isLocked)
        {{-- Lock icon with tooltip for locked records --}}
        <button type="button"
                title="{{ $lockerName }} is currently editing this request"
                class="inline-flex items-center p-2 text-xs bg-gray-400 text-white rounded-lg cursor-not-allowed dark:bg-gray-600 dark:text-gray-200"> {{-- Added dark mode classes --}}
            <svg class="{{ $size }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </button>

        {{-- Disabled delete button for locked records --}}
        <button disabled
                title="{{ $lockerName }} is currently editing this request"
                class="inline-flex items-center p-2 text-xs bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed dark:bg-gray-700 dark:text-gray-500"> {{-- Added dark mode classes --}}
            <x-heroicon-o-trash class="{{ $size }}" />
        </button>
    @else
        {{-- Edit button --}}
        @if($canEdit)
            <a href="{{ $editRoute ? route($editRoute, [$routeKeyName => $id]) : '#' }}"
               class="inline-flex items-center p-2 text-xs bg-emerald-100 text-emerald-700 rounded-lg hover:bg-emerald-200 dark:bg-emerald-800 dark:text-emerald-300 dark:hover:bg-emerald-700"> {{-- Added dark mode classes --}}
                <x-heroicon-o-pencil-square class="{{ $size }}" />
            </a>
        @else
            {{-- Disabled edit button --}}
            <button disabled
                    title="This record is currently being edited by another user"
                    class="inline-flex items-center p-2 text-xs bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed dark:bg-gray-700 dark:text-gray-500"> {{-- Added dark mode classes --}}
                <x-heroicon-o-pencil-square class="{{ $size }}" />
            </button>
        @endif

        {{-- Delete button --}}
        @if($canDelete)
            <button
                wire:click="dispatch('{{ $deleteEvent }}', { id: {{ $id }} })"
                wire:confirm="{{ $confirmMessage }}"
                class="inline-flex items-center p-2 text-xs bg-red-100 text-red-700 rounded-lg hover:bg-red-200 dark:bg-red-800 dark:text-red-300 dark:hover:bg-red-700"> {{-- Added dark mode classes --}}
                <x-heroicon-o-trash class="{{ $size }}" />
            </button>
        @endif
    @endif
</div>
