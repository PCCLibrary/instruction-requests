{{-- /views/instruction-requests/partials/create/async-schedule.blade.php --}}
<x-fieldset legend="" classes="bg-white dark:bg-gray-800 dark:border-gray-700 asynchronous-fields">
    <div class="space-y-6">
        <x-validated-input-date
            name="asynchronous_instruction_ready_date"
            label="Asynchronous instruction ready by"
            :value="old('asynchronous_instruction_ready_date')"
            help-text="Examples of asynchronous instruction: tutorials, videos, research guides, or a librarian embedded in Brightspace."
            required
            x-bind:min="$store.dateValidation.minDate"
            :validation="[
                'requiredFor' => ['asynchronous'],
                'customValidator' => 'validateAsyncDate',
                'messages' => [
                    'required' => 'Ready date is required',
                    'future' => 'Please select today or a future date'
                ]
            ]"
        />
    </div>
</x-fieldset>
