{{-- Notes Section --}}
<x-card title="Notes"
        class="bg-gray-50 dark:bg-gray-800 dark:border-gray-700"
        headerclass="dark:text-white"
>
            <div class="space-y-4">
                <!-- Class Notes (Textarea) - Always editable regardless of form state -->
                <x-input-textarea
                    name="class_notes"
                    label="Class Notes"
                    :value="$instructionRequest->detail->class_notes ?? null"
                    class="edit-field"
                />

                <!-- Assessment Notes (Textarea) - Always editable regardless of form state -->
                <x-input-textarea
                    name="assessment_notes"
                    label="Assessment Notes"
                    :value="$instructionRequest->detail->assessment_notes ?? null"
                    class="edit-field"
                />
            </div>
</x-card>
