{{-- /views/instruction-requests/partials/create/scheduling.blade.php --}}
    <x-fieldset legend="Schedule" classes="bg-white dark:bg-gray-800 dark:border-gray-700 on-campus-fields remote-fields">
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-validated-input-datetime
                    name="preferred_datetime"
                    label="Preferred Date & Time"
                    :value="old('preferred_datetime')"
                    help-text="Enter the date/time you prefer to have your instruction session."
                    required
                    x-bind:min="$store.dateValidation.minTime"
                    :validation="[
                        'requiredFor' => ['on-campus', 'remote'],
                        'customValidator' => 'validateDateTime',
                        'messages' => [
                            'required' => 'Preferred date and time is required',
                            'future' => 'Please select a time in the future'
                        ]
                    ]"
                />

                <x-validated-input-datetime
                    name="alternate_datetime"
                    label="Alternate Date & Time"
                    :value="old('alternate_datetime')"
                    help-text="Enter an alternate date/time for your instruction session."
                    x-bind:min="$store.dateValidation.minTime"
                    :validation="[
                        'requiredFor' => ['on-campus', 'remote'],
                        'customValidator' => 'validateDateTime',
                        'messages' => [
                            'future' => 'Please select a time in the future'
                        ]
                    ]"
                />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-validated-input-text
                    name="duration"
                    label="Duration (in minutes)"
                    :value="old('duration')"
                    type="number"
                    help-text="Enter the duration in minutes"
                    required
                    :validation="[
                        'requiredFor' => ['on-campus', 'remote'],
                        'customValidator' => 'validateDuration',
                        'messages' => [
                            'required' => 'Duration is required',
                            'invalid' => 'Please enter a whole number (integers only)',
                            'min' => 'Please enter a number greater than 1'
                        ]
                    ]"
                />

                <x-input-textarea
                    name="extra_time_with_class"
                    label="Do you need time to discuss non-library matters with your class?"
                    :value="old('extra_time_with_class')"
                />
            </div>
        </div>
    </x-fieldset>
