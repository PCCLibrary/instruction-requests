{{-- resources/views/instruction-requests/partials/create/date-time-fields.blade.php --}}
<x-card title="Date, Time and Duration" class="bg-gray-50 mb-4">
        <div class="space-y-4">
            {{-- Synchronous Instruction Fields --}}
            <div x-show="instructionType !== 'asynchronous'" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-input-datetime
                        name="preferred_datetime"
                        label="Preferred Date & Time"
                        :value="old('preferred_datetime')"
                        help-text="Enter the date/time you prefer to have your instruction session."
                        x-bind:min="$store.dateValidation.minTime"
                        x-bind:required="instructionType !== 'asynchronous'"
                    />

                    <x-input-datetime
                        name="alternate_datetime"
                        label="Alternate Date & Time"
                        :value="old('alternate_datetime')"
                        help-text="Enter an alternate date/time for your instruction session."
                        x-bind:min="$store.dateValidation.minTime"
                    />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-input-text
                        name="duration"
                        label="Duration (in minutes)"
                        :value="old('duration')"
                        type="number"
                        help-text="Enter the duration in minutes"
                        x-bind:required="instructionType !== 'asynchronous'"
                    />

                    <x-input-textarea
                        name="extra_time_with_class"
                        label="Do you need time to discuss non-library matters with your class?"
                        :value="old('extra_time_with_class')"
                    />
                </div>
            </div>

            {{-- Asynchronous Instruction Fields --}}
            <div x-show="instructionType === 'asynchronous'">
                <x-input-date
                    name="asynchronous_instruction_ready_date"
                    label="Asynchronous instruction ready by"
                    :value="old('asynchronous_instruction_ready_date')"
                    help-text="Examples of asynchronous instruction: tutorials, videos, research guides, or a librarian embedded in Brightspace."
                    x-bind:min="$store.dateValidation.minDate"
                    x-bind:required="instructionType === 'asynchronous'"
                />
            </div>
        </div>
</x-card>
