<x-card class="bg-teal-50 dark:bg-teal-950 mb-4 shadow-sm dark:border-gray-700"
        title="Create a New Instruction Request"
        headerclass="dark:text-white">

    <div class="p-2">
        <div class="flex items-center mb-4">
            <p class="text-gray-600 dark:text-gray-300 sm:max-w-md">
                Creating a new request will notify the instructor and campus librarians. Cancel to return to the list of instruction requests.
            </p>
        </div>
        <x-editor-actions
            route="{{ route('instructionRequests.index') }}"
            :showBack="true"
        />
    </div>
</x-card>
