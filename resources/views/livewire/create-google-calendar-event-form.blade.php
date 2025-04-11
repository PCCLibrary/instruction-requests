<div x-data="{ submitted: false }">
    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
        <div class="sm:flex sm:items-start">
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                    Schedule Library Instruction
                </h3>

                <form wire:submit="createEvent" class="mt-4" id="createGoogleCalendarEventForm">
                    @if (session()->has('success'))
                        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
                            <strong>Success!</strong> {{ session('success') }}
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                            <strong>Error!</strong> {{ session('error') }}
                        </div>
                    @endif
                    
                    @error('calendar')
                        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                            <strong>Error!</strong> {!! $message !!}
                        </div>
                    @enderror

                    <div class="space-y-4">
                        <!-- Event Title -->
                        <div>
                            <label for="eventName" class="block text-sm font-medium text-gray-700">Event Title</label>
                            <input type="text" id="eventName" wire:model="eventName"
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('eventName')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Start Time -->
                        <div>
                            <label for="startTime" class="block text-sm font-medium text-gray-700">Start Time</label>
                            <input type="datetime-local" id="startTime" wire:model="startTime"
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('startTime')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- End Time -->
                        <div>
                            <label for="endTime" class="block text-sm font-medium text-gray-700">End Time</label>
                            <input type="datetime-local" id="endTime" wire:model="endTime"
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('endTime')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Location -->
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                            <input type="text" id="location" wire:model="location"
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('location')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea id="description" wire:model="description" rows="3"
                                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
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
                                @click="$dispatch('close-modal'); document.querySelector('[x-data*=\'isOpen\']').__x.$data.isOpen = false;"
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
    // Add basic form monitoring
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, setting up basic calendar form monitoring');
        
        // Listen for form submission events for debugging
        const form = document.getElementById('createGoogleCalendarEventForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                console.log('Form submit event detected:', e.type);
                // Let Livewire handle the actual submission
                e.preventDefault();
            });
        }
    });
    
    // Listen for Livewire initialization and critical events
    document.addEventListener('livewire:initialized', function() {
        console.log('Livewire initialized, monitoring calendar form component');
        
        // Only monitor essential events for debugging
        try {
            // Log failures which are most important for debugging
            Livewire.hook('message.failed', (message, component) => {
                console.error('Livewire message failed:', message, 'Component:', component.id);
            });
        } catch (err) {
            console.error('Error setting up Livewire monitoring:', err);
        }
    });
</script>