{{-- Learning Outcomes Section --}}
<x-card title="By the time students receive library instruction they will have:" class="bg-gray-50 mb-4">
    <div class="grid grid-cols-2 gap-6">
        <div class="space-y-4">
            <x-input-checkbox
                name="received_assignment"
                label="Received Assignment"
                :checked="$instructionRequest->received_assignment"
{{--                disabled="!isEditing"--}}
                class="edit-field"
            />
            <x-input-checkbox
                name="selected_topics"
                label="Selected Topics"
                :checked="$instructionRequest->selected_topics"
{{--                disabled="!isEditing"--}}
                class="edit-field"
            />
            <x-input-checkbox
                name="explored_background"
                label="Explored Background"
                :checked="$instructionRequest->explored_background"
{{--                disabled="!isEditing"--}}
                class="edit-field"
            />
        </div>
        <div class="space-y-4">
            <x-input-checkbox
                name="written_draft"
                label="Written Draft"
                :checked="$instructionRequest->written_draft"
{{--                disabled="!isEditing"--}}
                class="edit-field"
            />
            <x-input-checkbox
                name="other_learning_outcome"
                label="Other Learning Outcome"
                :checked="$instructionRequest->other_learning_outcome"
{{--                disabled="!isEditing"--}}
                class="edit-field"
                x-ref="otherOutcomeCheckbox"
            />
        </div>
    </div>

    <div class="mt-4" x-show="$refs.otherOutcomeCheckbox && $refs.otherOutcomeCheckbox.checked">
        <x-textarea
            name="other_learning_outcome_description"
            label="Describe Other Learning Outcome"
            :value="$instructionRequest->other_learning_outcome_description"
{{--            disabled="!isEditing"--}}
            class="edit-field"
        />
    </div>
</x-card>
