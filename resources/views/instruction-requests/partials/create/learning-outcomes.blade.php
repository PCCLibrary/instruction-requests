{{-- /views/instruction-requests/partials/create/learning-outcomes.blade.php --}}
<x-fieldset legend="By the time students receive library instruction they will have:" classes="bg-white dark:bg-gray-800 dark:border-gray-700 on-campus-fields remote-fields">
    <div class="grid grid-cols-2 gap-6">
        <div class="space-y-4">
            <x-input-checkbox
                name="received_assignment"
                label="Received Assignment"
                :checked="old('received_assignment')"
            />

            <x-input-checkbox
                name="selected_topics"
                label="Selected Topics"
                :checked="old('selected_topics')"
            />

            <x-input-checkbox
                name="explored_background"
                label="Explored Background"
                :checked="old('explored_background')"
            />
        </div>

        <div class="space-y-4">
            <x-input-checkbox
                name="written_draft"
                label="Written Draft"
                :checked="old('written_draft')"
            />

            <div x-data="{ showOther: {{ old('other_learning_outcome') ? 'true' : 'false' }} }">
                <x-input-checkbox
                    name="other_learning_outcome"
                    label="Other Learning Outcome"
                    :checked="old('other_learning_outcome')"
                    @change="showOther = $event.target.checked"
                />

                <div x-show="showOther" class="mt-4">
                    <x-input-textarea
                        name="other_learning_outcome_description"
                        label="Describe Other Learning Outcome"
                        :value="old('other_learning_outcome_description')"
                        help-text="Please describe any additional learning outcomes"
                    />
                </div>
            </div>
        </div>
    </div>
</x-fieldset>
