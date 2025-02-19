{{-- Edit Toggle Button --}}
<div class="flex justify-end mb-4">
    <button type="button"
            @click="toggleEdit"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white"
            :class="isEditing ? 'bg-green-600 hover:bg-green-700' : 'bg-blue-600 hover:bg-blue-700'"
    >
        <template x-if="!isEditing">
            <x-heroicon-o-lock-closed class="h-4 w-4 mr-1.5" />
        </template>
        <template x-if="isEditing">
            <x-heroicon-o-lock-open class="h-4 w-4 mr-1.5" />
        </template>
        <span x-text="isEditing ? 'Lock Fields' : 'Edit Fields'"></span>
    </button>
</div>
