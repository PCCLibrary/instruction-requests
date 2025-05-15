<div>
    <div class="mb-4 bg-sky-100 rounded-lg shadow-sm p-4 border border-sky-200 dark:bg-gray-800 dark:border-gray-700"> {{-- Container dark mode bg and border kept --}}
        <div class="mb-2">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-white">Quick Filters</h2> {{-- Heading dark text kept --}}
        </div>
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="w-full sm:w-auto">
                <div class="flex flex-wrap items-center gap-2">
                    <div class="flex flex-wrap gap-2">
                        <button
                            wire:click="filterByLibrarian"
                            class="px-2 py-1 text-sm font-medium text-white bg-teal-600 border border-gray-300 rounded-md hover:bg-teal-700 dark:bg-teal-800 dark:hover:bg-teal-900 dark:border-gray-800" {{-- Added dark mode classes --}}
                        >
                            My Requests
                        </button>
                        <button
                            wire:click="filterExpiringReceived"
                            class="px-2 py-1 text-sm font-medium text-white bg-amber-600 border border-gray-300 rounded-md hover:bg-amber-700 dark:bg-amber-800 dark:hover:bg-amber-900 dark:border-gray-800" {{-- Added dark mode classes --}}
                        >
                            Expiring Received
                        </button>
                        <button
                            wire:click="filterRejected"
                            class="px-2 py-1 text-sm font-medium text-white bg-purple-600 border border-gray-300 rounded-md hover:bg-purple-700 dark:bg-purple-800 dark:hover:bg-purple-900 dark:border-gray-800" {{-- Added dark mode classes --}}
                        >
                            Rejected Requests
                        </button>
                        <button
                            wire:click="clearFilters"
                            class="px-2 py-1 text-sm font-medium text-red-600 dark:text-white bg-white dark:bg-red-800 border border-red-300 dark:border-red-700 rounded-md hover:bg-red-50 dark:hover:bg-red-900" {{-- Adjusted dark mode classes for text, bg, border, and hover --}}
                        >
                            Clear Filters
                        </button>
                    </div>

                    @if($showingMyRequests)
                        <h3 class="ml-4 font-bold text-gray-800 dark:text-gray-300"> {{-- Added dark mode text color --}}
                            Showing "My Requests"
                        </h3>
                    @elseif($showingExpiringReceived)
                        <h3 class="ml-4 font-bold text-gray-800 dark:text-gray-300"> {{-- Added dark mode text color --}}
                            Showing "Received requests expiring in 14 days"
                        </h3>
                    @elseif($showingRejected)
                        <h3 class="ml-4 font-bold text-gray-800 dark:text-gray-300"> {{-- Added dark mode text color --}}
                            Showing "Rejected Requests"
                        </h3>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
