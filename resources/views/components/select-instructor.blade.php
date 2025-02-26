<div
    x-show="openModal"
    class="fixed inset-0 z-10 overflow-y-auto"
    x-cloak
    @instructor-selected.window="
        openModal = false;
        // Get the data object - event.detail comes in as an array from Livewire
        const data = $event.detail[0];
        console.log('Instructor data received:', data);

        // Use document selectors instead of $refs
        const idField = document.getElementById('instructor_id');
        const nameField = document.getElementById('name');
        const displayNameField = document.getElementById('display_name');
        const pronounsField = document.getElementById('pronouns');
        const emailField = document.getElementById('email');
        const phoneField = document.getElementById('phone');

        // Update fields if they exist
        if (idField && data) idField.value = data.id;
        if (nameField && data) nameField.value = data.name;
        if (displayNameField && data) displayNameField.value = data.display_name;
        if (pronounsField && data) pronounsField.value = data.pronouns;
        if (emailField && data) emailField.value = data.email;
        if (phoneField && data) phoneField.value = data.phone;

        console.log('Updated instructor fields from event:', data);
    "
    @close-instructor-modal.window="openModal = false"
>
    <!-- Modal backdrop and container -->
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Modal background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
             aria-hidden="true"
             @click="openModal = false"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <!-- Use standard Livewire directive for Livewire 3 compatibility -->
            @livewire('instructor-selector', ['instructorId' => isset($instructorId) ? $instructorId : null], key('instructor-selector-' . (isset($instructorId) ? $instructorId : 'new')))
        </div>
    </div>
</div>
