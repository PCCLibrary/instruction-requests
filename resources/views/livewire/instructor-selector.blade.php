<div>
    <div class="mb-4">
        <h3 class="text-lg font-medium text-gray-900">Select Instructor</h3>
        <p class="text-sm text-gray-500 mt-1">
            Search for an existing instructor by name or email.
        </p>

        @if($selectedInstructor)
            <div class="mt-3 p-2 bg-cyan-50 border border-cyan-200 rounded-md">
                <p class="text-sm font-medium text-gray-700">Currently selected:
                    <span class="text-cyan-700">{{ $selectedInstructor->name }}</span>
                </p>
            </div>
        @endif
    </div>

    <div class="mb-4 relative">
        <label for="instructor-search" class="block text-sm font-medium text-gray-700">Search</label>
        <div class="relative mt-1">
            <input
                type="text"
                id="instructor-search"
                wire:model.live="search"
                placeholder="Start typing to search..."
                class="pr-10 focus:ring-cyan-500 focus:border-cyan-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                autofocus
            >
            @if(!empty($search))
                <button
                    type="button"
                    wire:click="clearSearch"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 hover:text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </button>
            @endif
        </div>
    </div>

    <div class="mt-4 mb-4 max-h-60 overflow-y-auto">
        @if($filteredInstructors->isEmpty())
            <div class="py-4 text-center text-gray-500">
                <p>No instructors found. Try a different search term.</p>
            </div>
        @else
            <ul class="divide-y divide-gray-200">
                @foreach($filteredInstructors as $instructor)
                    <li class="py-2 px-2 flex items-center hover:bg-gray-50 {{ $selectedInstructor && $selectedInstructor->id === $instructor->id ? 'bg-cyan-50' : '' }}">
                        <button
                            type="button"
                            wire:click="selectInstructor({{ $instructor->id }})"
                            class="w-full text-left flex items-start space-x-3"
                        >
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $instructor->name }}
                                    </p>
                                    @if($selectedInstructor && $selectedInstructor->id === $instructor->id)
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-cyan-600" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-500">
                                    {{ $instructor->display_name ?? $instructor->name }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ $instructor->email }}
                                </p>
                            </div>
                        </button>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
        <button
            type="button"
            wire:click="confirmSelection"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-cyan-600 text-base font-medium text-white hover:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 sm:col-start-2 sm:text-sm"
            {{ !$selectedInstructor ? 'disabled' : '' }}
        >
            Select
        </button>
        <button
            type="button"
            wire:click="cancelSelection"
            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 sm:mt-0 sm:col-start-1 sm:text-sm"
        >
            Cancel
        </button>
    </div>
</div>
