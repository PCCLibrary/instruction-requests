<div x-show="isOpen" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
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
    // Listen for the closeModal event from the Livewire component
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('closeModal', () => {
            // Close the modal using Alpine.js
            console.log('Close modal event received');
            
            // Find the div that contains the Alpine.js isOpen data
            const container = document.querySelector('[x-data*="isOpen"]');
            if (container) {
                // Set isOpen to false using Alpine.js
                Alpine.evaluate(container, 'isOpen = false');
            }
        });
    });
</script>
