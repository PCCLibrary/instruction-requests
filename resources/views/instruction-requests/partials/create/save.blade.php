

    <x-card title="" class="bg-emerald-100 mb-4">
<p class="text-gray-600 mb-4">
    Create a new instruction request. Creating a new request will notify the instructor an campus librarians. Cancel to return to the list of instruction requests.
</p>
        <x-editor-actions
            route="{{ route('instructionRequests.index') }}"
            :showBack="true"
        />
    </x-card>
