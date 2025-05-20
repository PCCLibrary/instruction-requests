<div
    x-data="{
        deleteModalOpen: false,
        handleDeleteSuccess() {
            // Close the modal when a successful deletion event is received
            this.deleteModalOpen = false;
        }
    }"
    @deletion-completed.window="handleDeleteSuccess()"
>
    {{-- Delete Button --}}
    <button
        type="button"
        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
        @click="deleteModalOpen = true"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
        Delete Calendar Event
    </button>

    {{-- Modal --}}
    <x-modal :show="'deleteModalOpen'" title="Delete Calendar Event">
        <div class="sm:flex sm:items-start">
            <div class="mx-auto shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <p class="text-sm text-gray-500">
                    Are you sure you want to delete this calendar event? This will set the request status back to <strong>accepted</strong>.
                </p>

                @if($instructionRequest->googleCalendarEvent)
                    <div class="mt-4 p-3 bg-gray-50 rounded-md">
                        <p class="text-sm font-medium text-gray-700">Event Details:</p>
                        <ul class="mt-1 text-sm text-gray-600 space-y-1">
                            <li>
                                <span class="font-medium">Title:</span>
                                {{ $instructionRequest->googleCalendarEvent->event_title ?? 'Unknown' }}
                            </li>
                            @if($instructionRequest->googleCalendarEvent->start_time)
                                <li>
                                    <span class="font-medium">Date:</span>
                                    {{ $instructionRequest->googleCalendarEvent->start_time->format('M d, Y g:i A') }}
                                </li>
                            @endif
                        </ul>
                    </div>
                @endif

                @if($errorMessage)
                    <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded-md">
                        <p class="text-sm text-red-600">{{ $errorMessage }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="mt-5 sm:mt-6 sm:flex sm:flex-row-reverse">
            <button
                type="button"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                wire:click="deleteEvent"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-75 cursor-not-allowed"
                :disabled="$wire.isProcessing"
            >
                <span wire:loading.remove>Delete</span>
                <span wire:loading>
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Deleting...
                </span>
            </button>
            <button
                type="button"
                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                @click="deleteModalOpen = false; document.dispatchEvent(new CustomEvent('close-modal'))"
                :disabled="$wire.isProcessing"
            >
                Cancel
            </button>
        </div>
    </x-modal>
</div>
