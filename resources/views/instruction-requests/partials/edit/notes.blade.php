{{-- Notes Section --}}
<x-card title="Notes" class="bg-gray-50">
            <div class="space-y-4" x-data="{
                get isDisabled() {
                    return !$store.formState.isEditing;
                }
            }">
                <!-- Class Notes (Textarea) -->
                <x-input-textarea
                    name="class_notes"
                    label="Class Notes"
                    :value="$instructionRequest->detail->class_notes ?? null"
                    class="edit-field"
                    x-bind:disabled="isDisabled"
                />

                <!-- Assessment Notes (Textarea) -->
                <x-input-textarea
                    name="assessment_notes"
                    label="Assessment Notes"
                    :value="$instructionRequest->detail->assessment_notes ?? null"
                    class="edit-field"
                    x-bind:disabled="isDisabled"
                />
            </div>
</x-card>
