{{-- Date and Time Fields Section --}}
<x-card title="Date, Time and Duration" class="bg-gray-50 mb-4">
    <div x-data="{ instructionType: '{{ $instructionRequest->instruction_type }}' }">
        {{-- Synchronous Instruction Fields --}}
        <div x-show="instructionType !== 'asynchronous'" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-input-datetime
                    name="preferred_datetime"
                    label="Preferred Date & Time"
                    :value="$instructionRequest->preferred_datetime ? $instructionRequest->preferred_datetime->format('Y/m/d h:i A') : null"
                    disabled="!isEditing"
                    class="edit-field"
                    helptext="Enter the date/time you prefer to have your instruction session."
                    required
                />

                <x-input-datetime
                    name="alternate_datetime"
                    label="Alternate Date & Time"
                    :value="$instructionRequest->alternate_datetime ? $instructionRequest->alternate_datetime->format('Y/m/d h:i A') : null"
                    disabled="!isEditing"
                    class="edit-field"
                    helptext="Enter an alternate date/time for your instruction session."
                />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-input-text
                    name="duration"
                    label="Duration (in minutes)"
                    :value="$instructionRequest->duration"
                    disabled="!isEditing"
                    class="edit-field"
                    type="number"
                    required
                />

                <x-input-textarea
                    name="extra_time_with_class"
                    label="Do you need time to discuss non-library matters with your class?"
                    :value="$instructionRequest->extra_time_with_class"
                    disabled="!isEditing"
                    class="edit-field"
                />
            </div>
        </div>

        {{-- Asynchronous Instruction Fields --}}
        <div x-show="instructionType === 'asynchronous'">
            <x-input-date
                name="asynchronous_instruction_ready_date"
                label="Asynchronous instruction ready by"
                :value="$instructionRequest->asynchronous_instruction_ready_date ? $instructionRequest->asynchronous_instruction_ready_date->format('Y/m/d') : null"
                disabled="!isEditing"
                class="edit-field"
                helptext="Examples of asynchronous instruction: tutorials, videos, research guides, or a librarian embedded in Brightspace."
                required
            />
        </div>
    </div>
</x-card>
