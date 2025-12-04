<div>
    {{-- Table Label --}}
    <div class="mb-2">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">My Active Requests</h2>
    </div>

    {{-- PowerGrid Table --}}
    <livewire:powergrid::powergrid :datasource="$datasource" :columns="$columns" :theme="$theme" />
</div>

@script
<script>
    // Use Livewire's event system to listen for custom events
    console.log('MyActiveRequestsTable: Setting up Livewire event listeners');

    Livewire.on('openScheduleModal', (event) => {
        console.log('MyActiveRequestsTable: Livewire.on openScheduleModal received', event);
        // event is the object { requestId: 181 }
        $wire.openScheduleModal(event.requestId);
    });

    Livewire.on('markInProgress', (event) => {
        console.log('MyActiveRequestsTable: Livewire.on markInProgress received', event);
        $wire.markInProgress(event.requestId);
    });

    Livewire.on('markComplete', (event) => {
        console.log('MyActiveRequestsTable: Livewire.on markComplete received', event);
        $wire.markComplete(event.requestId);
    });
</script>
@endscript

{{-- Google Calendar Modal (teleported to body) --}}
@teleport('body')
    @if($scheduleModalOpen && $scheduleRequestId)
        <div class="fixed z-50 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                {{-- Semi-transparent backdrop --}}
                <div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity"
                     wire:click="$set('scheduleModalOpen', false)"
                     aria-hidden="true"></div>

                {{-- Vertical alignment helper --}}
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                {{-- Modal panel --}}
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4" id="modal-title">
                                    Schedule Instruction Session
                                </h3>

                                {{-- DEBUG INFO --}}
                                <div class="mb-4 p-2 bg-yellow-100 text-yellow-900 text-sm">
                                    DEBUG: Livewire Modal Open = {{ $scheduleModalOpen ? 'TRUE' : 'FALSE' }}, Livewire Request ID = {{ $scheduleRequestId }}
                                </div>

                                @livewire('create-google-calendar-event-form', ['requestId' => $scheduleRequestId], key($scheduleRequestId))
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endteleport
