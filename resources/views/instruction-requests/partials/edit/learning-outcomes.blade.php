{{-- Learning Outcomes Section --}}
<x-card title="By the time students receive library instruction they will have:" class="bg-gray-50 dark:bg-gray-800 mb-4">
    <div x-data="{
        get isDisabled() {
            return !$store.formState.isSectionEditable('learningOutcomes');
        }
    }">
        <div class="grid grid-cols-2 gap-6">
            <div class="space-y-4">
                <x-input-checkbox
                    name="received_assignment"
                    label="Received Assignment"
                    :checked="$instructionRequest->received_assignment"
                    class="edit-field"
                    x-bind:class="isDisabled ? 'opacity-50 cursor-not-allowed' : ''"
                />
                <x-input-checkbox
                    name="selected_topics"
                    label="Selected Topics"
                    :checked="$instructionRequest->selected_topics"
                    class="edit-field"
                    x-bind:class="isDisabled ? 'opacity-50 cursor-not-allowed' : ''"
                />
                <x-input-checkbox
                    name="explored_background"
                    label="Explored Background"
                    :checked="$instructionRequest->explored_background"
                    class="edit-field"
                    x-bind:class="isDisabled ? 'opacity-50 cursor-not-allowed' : ''"
                />
            </div>
            <div class="space-y-4">
                <x-input-checkbox
                    name="written_draft"
                    label="Written Draft"
                    :checked="$instructionRequest->written_draft"
                    class="edit-field"
                    x-bind:class="isDisabled ? 'opacity-50 cursor-not-allowed' : ''"
                />
                <x-input-checkbox
                    name="other_learning_outcome"
                    label="Other Learning Outcome"
                    :checked="$instructionRequest->other_learning_outcome"
                    class="edit-field"
                    x-bind:class="isDisabled ? 'opacity-50 cursor-not-allowed' : ''"
                    x-ref="otherOutcomeCheckbox"
                />
            </div>
        </div>

        <div class="mt-4" x-show="$refs.otherOutcomeCheckbox && $refs.otherOutcomeCheckbox.checked">
            <x-textarea
                name="other_learning_outcome_description"
                label="Describe Other Learning Outcome"
                :value="$instructionRequest->other_learning_outcome_description"
                x-bind:readonly="isDisabled"
                class="edit-field"
                x-bind:class="isDisabled ? 'bg-gray-100 dark:bg-gray-700 cursor-not-allowed' : ''"
            />
        </div>
    </div>
</x-card>
