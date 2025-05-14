{{-- resources/views/instruction-requests/partials/create/instructor-info.blade.php --}}
<x-fieldset legend="Instructor Information" classes="bg-white dark:bg-gray-800">
    <div x-data="{ openModal: false }" class="space-y-6">
        {{-- Add hidden field for instructor_id --}}
        <input type="hidden" name="instructor_id" id="instructor_id" value="{{ old('instructor_id') }}">

        {{-- Add Select Instructor Button --}}
        <div class="mb-4">
            <button
                type="button"
                @click="openModal = true"
                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-800"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Select from Existing Instructors
            </button>
        </div>

        {{-- Instructor form fields --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-input-text
                name="name"
                id="name"
                label="Instructor Name"
                :value="old('name')"
                help-text="Your full name"
                required
            />

            <x-input-text
                name="display_name"
                id="display_name"
                label="Students refer to me as"
                :value="old('display_name')"
                help-text="How you would like students to address you"
            />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-input-text
                name="pronouns"
                id="pronouns"
                label="Pronouns"
                :value="old('pronouns')"
            />

            <x-input-text
                name="email"
                id="email"
                label="Email"
                type="email"
                :value="old('email')"
                required
            />

            <x-input-text
                name="phone"
                id="phone"
                label="Phone"
                type="tel"
                :value="old('phone')"
            />
        </div>

        {{-- Include the instructor selector component --}}
        <div
            x-show="openModal"
            class="fixed inset-0 z-10 overflow-y-auto"
            x-cloak
            @instructor-selected.window="
                openModal = false;
                // Get the data object - event.detail comes in as an array from Livewire
                const data = $event.detail[0];
                console.log('Instructor data received:', data);

                // Update input fields
                document.getElementById('instructor_id').value = data.id;
                document.getElementById('name').value = data.name;
                document.getElementById('display_name').value = data.display_name;
                document.getElementById('pronouns').value = data.pronouns;
                document.getElementById('email').value = data.email;
                document.getElementById('phone').value = data.phone;

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
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <!-- Use standard Livewire directive for Livewire 3 compatibility -->
                    @livewire('instructor-selector', key('instructor-selector-new'))
                </div>
            </div>
        </div>
    </div>
</x-fieldset>
