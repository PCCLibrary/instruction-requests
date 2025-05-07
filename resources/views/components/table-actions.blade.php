<div class="flex space-x-2">
    @if($canEdit)
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
