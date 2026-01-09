<div x-data="{
    checkDisabled() {
        const hasChanges = Alpine.store('formState')?.hasUnsavedChanges || false;
        return hasChanges;
    }
}">
    <button
        type="button"
        wire:click="markInProgress"
        :disabled="checkDisabled()"
        :class="{
            'opacity-50 cursor-not-allowed': checkDisabled(),
            'hover:bg-purple-700': !checkDisabled()
        }"
        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
    >
        <x-heroicon-o-play class="h-4 w-4 text-white mr-2" />
        Mark In Progress
    </button>

    <!-- Warning message for disabled state -->
    <div x-show="checkDisabled()" class="mt-2 text-sm text-amber-600 dark:text-amber-400" x-cloak>
        Please save changes before marking as in progress.
    </div>
</div>
