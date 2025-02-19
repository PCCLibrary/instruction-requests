{{-- /views/instruction-requests/partials/create/async-schedule.blade.php --}}
<x-fieldset legend="" classes="bg-white asynchronous-fields">
    <div class="space-y-6">
        <x-input-date
            name="asynchronous_instruction_ready_date"
            label="Asynchronous instruction ready by"
            :value="old('asynchronous_instruction_ready_date')"
            help-text="Examples of asynchronous instruction: tutorials, videos, research guides, or a librarian embedded in Brightspace."
            required
            x-bind:min="$store.dateValidation.minDate"
            x-bind:required="$store.createFormState[name]?.required"
            x-bind:disabled="$store.createFormState[name]?.disabled"
        />
    </div>
</x-fieldset>
