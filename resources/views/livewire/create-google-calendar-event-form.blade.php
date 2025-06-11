<div x-data="{ submitted: false }">
    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
        <div class="sm:flex sm:items-start">
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                    Schedule Library Instruction
                </h3>

                <form wire:submit="createEvent" class="mt-4" id="createGoogleCalendarEventForm">
                    @if (session()->has('success'))
                        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded toast-message">
                            <strong>Success!</strong> {{ session('success') }}
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded toast-message">
                            <strong>Error!</strong> {{ session('error') }}
                        </div>
                    @endif

                    @error('calendar')
                        <div class="mb-4 bg-red-50 border-l-4 border-red-400 rounded-r-lg shadow-sm">
                            <div class="p-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <h3 class="text-sm font-medium text-red-800">
                                            Calendar Event Creation Failed
                                        </h3>
                                        <div class="mt-2 text-sm text-red-700">
                                            <p>{!! $message !!}</p>
                                        </div>
                                        @if(strpos($message, 'writer access') !== false)
                                            <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded">
                                                <div class="flex items-start">
                                                    <svg class="h-4 w-4 text-blue-400 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <div class="text-xs text-blue-800">
                                                        <p class="font-medium">Need Help?</p>
                                                        <p class="mt-1">This appears to be a calendar permissions issue. Please contact <strong>DST (503-977-8227)</strong> and reference this error. They can help resolve calendar access permissions.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif(strpos($message, 'configuration') !== false)
                                            <div class="mt-3 p-3 bg-amber-50 border border-amber-200 rounded">
                                                <div class="flex items-start">
                                                    <svg class="h-4 w-4 text-amber-400 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17a1 1 0 01-.293.707L11 20.414A1 1 0 0110 20v-4.586a1 1 0 00-.293-.707L3.293 8.293A1 1 0 013 7.586V4z"></path>
                                                    </svg>
                                                    <div class="text-xs text-amber-800">
                                                        <p class="font-medium">Configuration Issue</p>
                                                        <p class="mt-1">There's an issue with the calendar setup for this campus. Please contact your administrator to review the calendar configuration.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif(strpos($message, 'impersonation') !== false || strpos($message, 'Impersonation') !== false)
                                            <div class="mt-3 p-3 bg-purple-50 border border-purple-200 rounded">
                                                <div class="flex items-start">
                                                    <svg class="h-4 w-4 text-purple-400 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    </svg>
                                                    <div class="text-xs text-purple-800">
                                                        <p class="font-medium">System Configuration Required</p>
                                                        <p class="mt-1">This is a system-level configuration issue that requires administrator action. Please contact the system administrator to configure the impersonation settings.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="mt-3 p-3 bg-gray-50 border border-gray-200 rounded">
                                                <div class="flex items-start">
                                                    <svg class="h-4 w-4 text-gray-400 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                    </svg>
                                                    <div class="text-xs text-gray-700">
                                                        <p class="font-medium">Retry Suggestion</p>
                                                        <p class="mt-1">This may be a temporary issue. Please wait a few minutes and try again. If the problem persists, contact support.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @enderror

                    <div class="space-y-4">
                        <!-- Event Title -->
                        <div>
                            <label for="eventName" class="block text-sm font-medium text-gray-700">Event Title</label>
                            <input type="text" id="eventName" wire:model="eventName"
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('eventName')
                                <p class="mt-1 text-sm text-red-600 toast-message">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Start Time -->
                        <div>
                            <label for="startTime" class="block text-sm font-medium text-gray-700">Start Time</label>
                            <input type="datetime-local" id="startTime" wire:model="startTime"
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('startTime')
                                <p class="mt-1 text-sm text-red-600 toast-message">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- End Time -->
                        <div>
                            <label for="endTime" class="block text-sm font-medium text-gray-700">End Time</label>
                            <input type="datetime-local" id="endTime" wire:model="endTime"
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('endTime')
                                <p class="mt-1 text-sm text-red-600 toast-message">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Location -->
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                            <input type="text" id="location" wire:model="location"
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('location')
                                <p class="mt-1 text-sm text-red-600 toast-message">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea id="description" wire:model="description" rows="3"
                                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600 toast-message">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Attendees -->
                        <div class="bg-gray-50 rounded p-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Attendees</h4>
                            <ul class="space-y-1 text-sm text-gray-600">
                                @if($instructorEmail)
                                <li class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Instructor: {{ $instructorEmail }}
                                </li>
                                @endif

                                @if($librarianEmail)
                                <li class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Librarian: {{ $librarianEmail }}
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>

                    <div class="mt-5 sm:mt-6 sm:flex sm:flex-row-reverse">
                        <button type="button"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm"
                                wire:loading.attr="disabled"
                                id="submit-calendar-event"
                                x-bind:disabled="submitted"
                                @click="if (!submitted) { $wire.createEvent(); submitted = true; }">
                            <span wire:loading.remove x-show="!submitted">Create Calendar Event</span>
                            <span wire:loading x-show="submitted">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Creating...
                        </span>
                        </button>
                        <button type="button"
                                @click="
                                  $dispatch('close-modal');
                                  // Safely try to close the modal using parent element
                                  const parentEl = document.querySelector('[x-data*=\'isOpen\']');
                                  if (parentEl && parentEl.__x) {
                                    parentEl.__x.$data.isOpen = false;
                                  }
                                  // Clean up toast messages
                                  document.querySelectorAll('.toast-message').forEach(toast => toast.remove());
                                "
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:initialized', function() {
        // Monitor only critical errors without verbose logging
        Livewire.hook('message.failed', (message, component) => {
            // Only log actual failures, not verbose debugging
            if (message.errors && Object.keys(message.errors).length > 0) {
                console.error('Calendar form error:', message.errors);
            }
        });
    });
</script>
