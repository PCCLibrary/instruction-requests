<div>
    <div class="mb-4 bg-white rounded-lg shadow-sm p-4 border border-gray-200">
        <div class="mb-2">
            <h2 class="text-lg font-semibold text-gray-700">Quick Filters</h2>
        </div>
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="w-full sm:w-auto">
                <div class="flex flex-wrap items-center gap-2">
                    <div class="flex flex-wrap gap-2">
                        <button
                            wire:click="filterByLibrarian"
                            class="px-4 py-2 text-sm font-medium text-white bg-teal-600 border border-gray-300 rounded-md hover:bg-teal-700"
                        >
                            My Requests
                        </button>
                        <button
                            wire:click="filterExpiringReceived"
                            class="px-4 py-2 text-sm font-medium text-white bg-amber-600 border border-gray-300 rounded-md hover:bg-amber-700"
                        >
                            Expiring Received
                        </button>
                        <button
                            wire:click="filterRejected"
                            class="px-4 py-2 text-sm font-medium text-white bg-purple-600 border border-gray-300 rounded-md hover:bg-purple-700"
                        >
                            Rejected Requests
                        </button>
                        <button
                            wire:click="clearFilters"
                            class="px-4 py-2 text-sm font-medium text-red-600 bg-white border border-red-300 rounded-md hover:bg-red-50"
                        >
                            Clear Filters
                        </button>
                    </div>

                    @if($showingMyRequests)
                        <h3 class="ml-4 font-bold text-gray-800">
                            Showing "My Requests"
                        </h3>
                    @elseif($showingExpiringReceived)
                        <h3 class="ml-4 font-bold text-gray-800">
                            Showing "Expiring Received Requests"
                        </h3>
                    @elseif($showingRejected)
                        <h3 class="ml-4 font-bold text-gray-800">
                            Showing "Rejected Requests"
                        </h3>
                    @endif
                </div>
            </div>

{{--            <a href="{{ route('instructionRequests.create') }}"--}}
{{--               class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 rounded-md font-semibold text-xs text-white uppercase tracking-widest transition-colors">--}}
{{--                <svg xmlns="http://www.w3.org/2000/svg"--}}
{{--                     class="h-4 w-4 mr-2"--}}
{{--                     fill="none"--}}
{{--                     viewBox="0 0 24 24"--}}
{{--                     stroke="currentColor">--}}
{{--                    <path stroke-linecap="round"--}}
{{--                          stroke-linejoin="round"--}}
{{--                          stroke-width="2"--}}
{{--                          d="M12 4v16m8-8H4" />--}}
{{--                </svg>--}}
{{--                Create New Request--}}
{{--            </a>--}}
        </div>
    </div>
</div>
