{{-- Custom Export Buttons - Replaces PowerGrid's default dropdown --}}
@if(data_get($setUp, 'exportable'))
    <div class="flex items-center space-x-2">
        <span class="text-sm text-gray-700 dark:text-gray-300">
            Export {{ $this->total }} records:
        </span>

        @if (in_array('xlsx', data_get($setUp, 'exportable.type')))
            <button
                wire:click.prevent="exportToXLS"
                class="px-3 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:bg-green-700 dark:hover:bg-green-600 transition duration-150 ease-in-out"
            >
                XLS
            </button>
        @endif

        @if (in_array('csv', data_get($setUp, 'exportable.type')))
            <button
                wire:click.prevent="exportToCsv"
                class="px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-700 dark:hover:bg-blue-600 transition duration-150 ease-in-out"
            >
                CSV
            </button>
        @endif
    </div>
@endif
