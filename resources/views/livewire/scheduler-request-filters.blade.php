<div x-data="{ filtersExpanded: true }">
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
        <!-- Filter Header -->
        <div class="bg-teal-500 dark:bg-teal-600 px-4 py-3 rounded-t-lg flex items-center justify-between">
            <h3 class="text-lg font-medium text-white">Request Filters</h3>
            <button @click="filtersExpanded = !filtersExpanded" class="text-white hover:text-gray-200">
                <svg x-show="!filtersExpanded" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
                <svg x-show="filtersExpanded" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                </svg>
            </button>
        </div>

        <!-- Filter Content -->
        <div x-show="filtersExpanded" x-collapse class="p-4">
            <!-- Quick Filter Buttons -->
            <div class="flex flex-wrap gap-2">
                <button
                    wire:click="filterUnassigned"
                    class="px-3 py-2 text-sm font-medium text-white bg-blue-500 border border-blue-500 rounded-md hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-500"
                >
                    Unassigned Requests
                </button>
                <button
                    wire:click="filterNoPreference"
                    class="px-3 py-2 text-sm font-medium text-white bg-indigo-500 border border-indigo-500 rounded-md hover:bg-indigo-600 dark:bg-indigo-600 dark:hover:bg-indigo-500"
                >
                    No Preference
                </button>
                <button
                    wire:click="filterExpiringUnassigned"
                    class="px-3 py-2 text-sm font-medium text-white bg-amber-500 border border-amber-500 rounded-md hover:bg-amber-600 dark:bg-amber-600 dark:hover:bg-amber-500"
                >
                    Expiring Unassigned
                </button>
                <button
                    wire:click="filterRejected"
                    class="px-3 py-2 text-sm font-medium text-white bg-slate-500 border border-slate-500 rounded-md hover:bg-slate-600 dark:bg-slate-600 dark:hover:bg-slate-500"
                >
                    Rejected Requests
                </button>
            </div>

            <!-- Clear All Filters Button -->
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                <button wire:click="clearFilters" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm font-medium">
                    Clear Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Active Filter Pills -->
    @if($showingUnassigned || $showingNoPreference || $showingExpiringUnassigned || $showingRejected)
        <div class="mb-4 flex flex-wrap gap-2">
            @if($showingUnassigned)
                <span class="inline-flex items-center gap-2 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 px-3 py-1 rounded-full text-sm">
                    Showing: Unassigned Requests
                    <button wire:click="clearFilters" class="hover:text-blue-600 dark:hover:text-blue-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </span>
            @endif

            @if($showingNoPreference)
                <span class="inline-flex items-center gap-2 bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 px-3 py-1 rounded-full text-sm">
                    Showing: No Preference Requests
                    <button wire:click="clearFilters" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </span>
            @endif

            @if($showingExpiringUnassigned)
                <span class="inline-flex items-center gap-2 bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200 px-3 py-1 rounded-full text-sm">
                    Showing: Expiring Unassigned Requests (14 days)
                    <button wire:click="clearFilters" class="hover:text-amber-600 dark:hover:text-amber-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </span>
            @endif

            @if($showingRejected)
                <span class="inline-flex items-center gap-2 bg-slate-100 text-slate-800 dark:bg-slate-900 dark:text-slate-200 px-3 py-1 rounded-full text-sm">
                    Showing: Rejected Requests
                    <button wire:click="clearFilters" class="hover:text-slate-600 dark:hover:text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </span>
            @endif
        </div>
    @endif
</div>
