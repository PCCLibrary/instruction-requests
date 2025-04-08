<div>
    <button
        type="button"
        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
        wire:click="$dispatch('deleteEvent', {{ $instructionRequest->id }})"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
        Delete Calendar Event
    </button>

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('deleteEvent', (requestId) => {
                if (confirm('Are you sure you want to delete this calendar event? This will set the request status back to "accepted".')) {
                    @this.call('deleteEvent', requestId);
                }
            });
        });
    </script>
</div>
