<div>
    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
        <div class="sm:flex sm:items-start">
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                    Schedule Library Instruction
                </h3>

                <form wire:submit="createEvent" wire:submit.prevent="createEvent" class="mt-4" id="createGoogleCalendarEventForm" x-data="{ submitted: false }" x-on:submit="console.log('Form submitted'); submitted = true;">
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
                            <button type="submit"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm"
                                    wire:loading.attr="disabled"
                                    id="submit-calendar-event"
                                    x-bind:disabled="submitted"
                                    x-on:click="console.log('Submit button clicked directly'); if (!submitted) { $wire.createEvent(); submitted = true; }">
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
    // Add event listeners when the page loads
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, setting up calendar form listeners');
        
        const form = document.getElementById('createGoogleCalendarEventForm');
        const submitButton = document.getElementById('submit-calendar-event');
        
        if (form) {
            console.log('Found calendar event form, adding listeners');
            
            // Listen for form submission
            form.addEventListener('submit', function(e) {
                console.log('Form submit event triggered via DOM', e);
                
                // Prevent default and handle manually to ensure it works
                e.preventDefault();
                
                // Get Livewire component and call directly
                const componentEl = form.closest('[wire\\:id]');
                if (componentEl) {
                    const componentId = componentEl.getAttribute('wire:id');
                    console.log('Found Livewire component:', componentId);
                    
                    // Try both direct call methods
                    tryCallLivewireComponent(componentId);
                }
            });
        }
        
        if (submitButton) {
            console.log('Found submit button, adding direct click listener');
            submitButton.addEventListener('click', function(e) {
                console.log('Submit button clicked directly', e);
                
                // Get the Livewire component ID
                const componentId = form.closest('[wire\\:id]')?.getAttribute('wire:id');
                console.log('Livewire component ID:', componentId);
                
                // Try to call the Livewire component directly - if this fails, the button's x-on:click will handle it
                if (componentId) {
                    setTimeout(() => {
                        tryCallLivewireComponent(componentId);
                    }, 100);
                }
            });
        }
        
        // Helper function to try multiple ways of calling the Livewire component
        function tryCallLivewireComponent(componentId) {
            if (!componentId || !window.Livewire) {
                console.error('Cannot call Livewire component - missing ID or Livewire not initialized');
                return;
            }
            
            console.log('Attempting multiple methods to call Livewire component:', componentId);
            
            // Methods to try (in order of preference)
            const methods = [
                // Method 1: Livewire 3 style component call
                () => {
                    console.log('Trying Livewire.call method...');
                    if (typeof window.Livewire.call === 'function') {
                        window.Livewire.call(componentId, 'createEvent');
                        return true;
                    }
                    return false;
                },
                
                // Method 2: Direct component method call
                () => {
                    console.log('Trying component.call method...');
                    const component = window.Livewire.find(componentId);
                    if (component && typeof component.call === 'function') {
                        component.call('createEvent');
                        return true;
                    }
                    return false;
                },
                
                // Method 3: Livewire 3 dispatch
                () => {
                    console.log('Trying Livewire.dispatch method...');
                    if (typeof window.Livewire.dispatch === 'function') {
                        window.Livewire.dispatch('createEvent', {id: componentId});
                        return true;
                    }
                    return false;
                },
                
                // Method 4: Alternate direct method
                () => {
                    console.log('Trying direct Livewire component access...');
                    try {
                        const component = window.Livewire.find(componentId);
                        if (component) {
                            // Try accessing the createCalendarEventDirectly method
                            if (typeof component.createCalendarEventDirectly === 'function') {
                                component.createCalendarEventDirectly();
                                return true;
                            }
                            // Try the emit method
                            if (typeof component.emit === 'function') {
                                component.emit('createEvent');
                                return true;
                            }
                        }
                    } catch (err) {
                        console.error('Error accessing component directly:', err);
                    }
                    return false;
                }
            ];
            
            // Try each method until one succeeds
            for (const method of methods) {
                try {
                    if (method()) {
                        console.log('Successfully called Livewire component!');
                        return;
                    }
                } catch (err) {
                    console.error('Error calling Livewire method:', err);
                }
            }
            
            console.error('All methods to call Livewire component failed');
        }
    });
    
    // Listen for Livewire events
    document.addEventListener('livewire:initialized', function() {
        console.log('Livewire initialized, adding event listeners');
        
        // Listen for Livewire events
        try {
            Livewire.hook('component.initialized', (component) => {
                console.log('Livewire component initialized:', component.id);
            });
            
            Livewire.hook('message.sent', (message, component) => {
                console.log('Livewire message sent:', message, 'Component:', component.id);
            });
            
            Livewire.hook('message.failed', (message, component) => {
                console.error('Livewire message failed:', message, 'Component:', component.id);
            });
            
            Livewire.hook('message.received', (message, component) => {
                console.log('Livewire message received:', message, 'Component:', component.id);
            });
            
            Livewire.hook('element.initialized', (el, component) => {
                console.log('Livewire element initialized:', el, 'Component:', component.id);
            });
        } catch (err) {
            console.error('Error setting up Livewire hooks:', err);
        }
    });
</script>
