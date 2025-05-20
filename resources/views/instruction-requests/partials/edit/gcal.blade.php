<div
     x-show="isOpen"
     x-cloak
     class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
     aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
             @click="isOpen = false"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div
            class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">

            @livewire('create-google-calendar-event-form', ['requestId' => $instructionRequest->id])

        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        // Listen for Livewire event to refresh and close
        Livewire.on('googleCalendarEventCreated', (data) => {
            // First dispatch the close-modal event
            document.dispatchEvent(new CustomEvent('close-modal'));

            // Also try to directly close the modal using Alpine
            try {
                // Find parent container with isOpen
                const parent = document.querySelector('[x-data*="isOpen"]');
                if (parent && parent.__x) {
                    // Access the Alpine component and set isOpen to false
                    parent.__x.$data.isOpen = false;
                }
            } catch (err) {
                console.error('Error closing modal via Alpine:', err);
            }

            // Delay the page reload to allow Livewire to finish
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        });
    });
</script>
