{{-- resources/views/instruction-requests/partials/instructor-info.blade.php --}}
<x-card title="Instructor Information" class="bg-white mb-4 instructor-info-container">
    {{-- Hidden field for instructor_id --}}
    <input type="hidden" name="instructor_id" id="instructor_id" value="{{ $instructionRequest->instructor_id }}">

    <div x-data="{
        openModal: false,
        get isDisabled() {
            return !$store.formState.isEditing;
        }
    }">
        {{-- Select Instructor Button (only shown in edit mode) --}}
        <div class="mb-4" x-show="$store.formState.isEditing">
            <button
                type="button"
                @click="openModal = true"
                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-800"
            >
                <x-heroicon-o-user class="h-4 w-4 mr-1.5" />
                Select from Existing Instructors
            </button>
        </div>

        {{-- Form fields (editable only in edit mode) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <x-input-text
                    name="name"
                    id="name"
                    label="Instructor Name"
                    :value="$instructionRequest->instructor->name"
                    required
                    x-bind:disabled="isDisabled"
                />
            </div>
            <div>
                <x-input-text
                    name="display_name"
                    id="display_name"
                    label="Preferred Name"
                    :value="$instructionRequest->instructor->display_name"
                    required
                    x-bind:disabled="isDisabled"
                />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
            <div>
                <x-input-text
                    name="pronouns"
                    id="pronouns"
                    label="Pronouns"
                    :value="$instructionRequest->instructor->pronouns"
                    x-bind:disabled="isDisabled"
                />
            </div>
            <div>
                <x-input-text
                    name="email"
                    id="email"
                    label="Email"
                    type="email"
                    :value="$instructionRequest->instructor->email"
                    required
                    x-bind:disabled="isDisabled"
                />
            </div>
            <div>
                <x-input-text
                    name="phone"
                    id="phone"
                    label="Phone"
                    type="tel"
                    :value="$instructionRequest->instructor->phone"
                    x-bind:disabled="isDisabled"
                />
            </div>
        </div>

        {{-- View Instructor Link (shown in both modes) --}}
        <div class="mt-4 flex justify-end">
            <a href="{{ route('instructors.edit', $instructionRequest->instructor->id) }}"
               class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-white bg-cyan-600 hover:bg-cyan-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-800"
               target="_blank">
                <x-heroicon-o-eye class="h-4 w-4 mr-1.5" />
                View Instructor Profile
            </a>
        </div>

        {{-- Include the instructor selector component --}}
        <x-select-instructor :instructorId="$instructionRequest->instructor_id" />
    </div>
</x-card>
