<div class="flex space-x-2">
    @if($canEdit)
        <a href="{{ $editRoute ? route($editRoute, ['id' => $row->id]) : '#' }}"
           class="inline-flex items-center p-2 text-xs bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
            <x-heroicon-o-pencil-square class="{{ $size }}" />
        </a>
    @endif

    @if($canDelete)
        <button
            wire:click="dispatch('{{ $deleteEvent }}', { id: {{ $row->id }} })"
            class="inline-flex items-center p-2 text-xs bg-red-100 text-red-700 rounded-lg hover:bg-red-200">
            <x-heroicon-o-trash class="{{ $size }}" />
        </button>
    @endif
</div>
