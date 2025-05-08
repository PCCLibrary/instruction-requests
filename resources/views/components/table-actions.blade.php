<div class="flex space-x-2">
    @if(isset($isLocked) && $isLocked)
        {{-- Lock icon with tooltip for locked records --}}
        <button type="button"
                title="{{ $lockerName }} is currently editing this request"
                class="inline-flex items-center p-2 text-xs bg-gray-400 text-white rounded-lg cursor-not-allowed">
            <svg class="{{ $size }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </button>
    @elseif($canEdit)
        <a href="{{ $editRoute ? route($editRoute, [$routeKeyName => $id]) : '#' }}"
           class="inline-flex items-center p-2 text-xs bg-emerald-100 text-emerald-700 rounded-lg hover:bg-emerald-200">
            <x-heroicon-o-pencil-square class="{{ $size }}" />
        </a>
    @else
        {{-- Disabled edit button --}}
        <button disabled
                title="This record is currently being edited by another user"
                class="inline-flex items-center p-2 text-xs bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed">
            <x-heroicon-o-pencil-square class="{{ $size }}" />
        </button>
    @endif

    @if($canDelete)
        <button
            wire:click="dispatch('{{ $deleteEvent }}', { id: {{ $id }} })"
            class="inline-flex items-center p-2 text-xs bg-red-100 text-red-700 rounded-lg hover:bg-red-200">
            <x-heroicon-o-trash class="{{ $size }}" />
        </button>
    @endif
</div>
