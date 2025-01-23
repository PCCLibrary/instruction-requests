<div class="flex space-x-2">
    @if($canEdit)
        @if(str_contains($editRoute, 'instructionRequests'))
            <a href="{{ $editRoute ? route($editRoute, ['id' => $id]) : '#' }}"
               class="inline-flex items-center p-2 text-xs bg-emerald-100 text-emerald-700 rounded-lg hover:bg-emerald-200">
                <x-heroicon-o-pencil-square class="{{ $size }}" />
            </a>
        @else
            <a href="{{ $editRoute ? route($editRoute, [$routeKeyName => $id]) : '#' }}"
               class="inline-flex items-center p-2 text-xs bg-emerald-100 text-emerald-700 rounded-lg hover:bg-emerald-200">
                <x-heroicon-o-pencil-square class="{{ $size }}" />
            </a>
        @endif
    @endif

    @if($canDelete)
        <button
            wire:click="dispatch('{{ $deleteEvent }}', { id: {{ $id }} })"
            class="inline-flex items-center p-2 text-xs bg-red-100 text-red-700 rounded-lg hover:bg-red-200">
            <x-heroicon-o-trash class="{{ $size }}" />
        </button>
    @endif
</div>
