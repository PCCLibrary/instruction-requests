<div>
    {{-- GOOGLE CALENDAR MODAL --}}
    @if($scheduleModalOpen && $scheduleRequestId)
        <div class="fixed z-50 inset-0 overflow-y-auto"
             aria-labelledby="modal-title"
             role="dialog"
             aria-modal="true">

            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75"
                 wire:click="closeModal"></div>

            {{-- Modal --}}
            <div class="relative z-10 flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                {{-- Modal panel --}}
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">

                    {{-- Modal header --}}
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4" id="modal-title">
                                    Schedule Instruction Session
                                </h3>

                                {{-- Google Calendar Event Form Component --}}
                                @livewire('create-google-calendar-event-form', [
                                    'requestId' => $scheduleRequestId
                                ], key('schedule-modal-' . $scheduleRequestId))
                            </div>
                        </div>
                    </div>

                    {{-- Modal footer with close button --}}
                    <div class="bg-gray-50 dark:bg-gray-900 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button"
                                wire:click="closeModal"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
