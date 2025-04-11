<div x-data="{
    isOpen: false,
    checkDisabled() {
        const hasChanges = Alpine.store('formState')?.hasUnsavedChanges || false;
        const hasDuration = Alpine.store('formState')?.hasDuration() || false;
        return hasChanges || !hasDuration;
    }
}">
    {{-- Schedule Button --}}
    <button
        type="button"
        @click="!checkDisabled() && (isOpen = true)"
        x-bind:class="{
            'opacity-50 cursor-not-allowed': checkDisabled(),
            'hover:bg-indigo-700': !checkDisabled()
        }"
        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
    >
        <x-heroicon-o-calendar-date-range class="h-4 w-4 text-white mr-2" />
        Create Google Calendar Event
    </button>

    {{-- Warning Messages --}}
    <div x-show="checkDisabled()" class="mt-2 text-sm text-amber-600" x-cloak>
        <div x-show="Alpine.store('formState')?.hasUnsavedChanges">
            Please save changes before scheduling.
        </div>
        <div x-show="!Alpine.store('formState')?.hasDuration()">
            Please enter a valid duration before scheduling.
        </div>
    </div>

    {{-- Modal Wrapper --}}
    <x-modal :show="'isOpen'" title="Create Google Calendar Event">
        @livewire('create-google-calendar-event-form', ['requestId' => $instructionRequest->id])
    </x-modal>
</div>
