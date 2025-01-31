<div>
    <div class="mb-4 space-y-2">
        <div class="flex flex-wrap gap-2">
            <button
                wire:click="filterByLibrarian"
                class="px-4 py-2 text-sm font-medium text-white bg-teal-600 border border-gray-300 rounded-md hover:bg-teal-700"
            >
                My Requests
            </button>

{{--            <button--}}
{{--                wire:click="filterByCampus('CAS')"--}}
{{--                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"--}}
{{--            >--}}
{{--                Cascade--}}
{{--            </button>--}}

{{--            <button--}}
{{--                wire:click="filterByStatus('received')"--}}
{{--                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"--}}
{{--            >--}}
{{--                Received--}}
{{--            </button>--}}

            <button
                wire:click="clearFilters"
                class="px-4 py-2 text-sm font-medium text-red-600 bg-white border border-red-300 rounded-md hover:bg-red-50"
            >
                Reset Table
            </button>
        </div>
    </div>
</div>
