<div x-data="{ filtersExpanded: true }">
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
        <div class="bg-purple-500 dark:bg-purple-600 px-4 py-3 rounded-t-lg flex items-center justify-between">
            <h3 class="text-lg font-medium text-white">Quick Filters</h3>
            <button @click="filtersExpanded = !filtersExpanded" class="text-white hover:text-gray-200">
                <svg x-show="!filtersExpanded" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
                <svg x-show="filtersExpanded" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                </svg>
            </button>
        </div>

        <div x-show="filtersExpanded" x-collapse class="p-4">
            <div class="flex flex-wrap gap-2">
                <button
                    wire:click="filterByLibrarian"
                    class="px-3 py-2 text-sm font-medium text-white bg-teal-500 border border-teal-500 rounded-md hover:bg-teal-600 dark:bg-teal-600 dark:hover:bg-teal-500"
                >
                    My Requests
                </button>
                <button
                    wire:click="filterExpiringReceived"
                    class="px-3 py-2 text-sm font-medium text-white bg-amber-500 border border-amber-500 rounded-md hover:bg-amber-600 dark:bg-amber-600 dark:hover:bg-amber-500"
                >
                    Expiring Received
                </button>
                <button
                    wire:click="filterRejected"
                    class="px-3 py-2 text-sm font-medium text-white bg-pink-500 border border-pink-500 rounded-md hover:bg-pink-600 dark:bg-pink-600 dark:hover:bg-pink-500"
                >
                    Rejected Requests
                </button>
            </div>

            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                <button wire:click="clearFilters" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm font-medium">
                    Clear Filters
                </button>
            </div>
        </div>
    </div>

    @if($showingMyRequests || $showingExpiringReceived || $showingRejected)
        <div class="mb-4 flex flex-wrap gap-2">
            @if($showingMyRequests)
                <span class="inline-flex items-center gap-2 bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-200 px-3 py-1 rounded-full text-sm">
                    Showing: My Requests
                    <button wire:click="clearFilters" class="hover:text-teal-600 dark:hover:text-teal-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </span>
            @endif

            @if($showingExpiringReceived)
                <span class="inline-flex items-center gap-2 bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200 px-3 py-1 rounded-full text-sm">
                    Showing: Received requests expiring in 14 days
                    <button wire:click="clearFilters" class="hover:text-amber-600 dark:hover:text-amber-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </span>
            @endif

            @if($showingRejected)
                <span class="inline-flex items-center gap-2 bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-200 px-3 py-1 rounded-full text-sm">
                    Showing: Rejected Requests
                    <button wire:click="clearFilters" class="hover:text-pink-600 dark:hover:text-pink-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </span>
            @endif
        </div>
    @endif
</div>
