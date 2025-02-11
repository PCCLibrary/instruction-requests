{{-- ADA Provisions Section --}}
<x-card title="ADA Provisions" class="bg-gray-50 mb-4">
        <div class="space-y-4">
            <div class="flex items-start space-x-4">
                <div class="w-1/3">
                    <x-input-checkbox
                        name="ada_provisions_needed"
                        label="ADA Provisions Needed"
                        :checked="$instructionRequest->ada_provisions_needed"
                        disabled="!isEditing"
                        class="edit-field"
                    />
                </div>
                <div class="w-2/3">
                    <x-input-textarea
                        name="ada_provisions_description"
                        label="Describe the ADA accommodations needed for your class"
                        :value="$instructionRequest->ada_provisions_description"
                        disabled="!isEditing"
                        class="edit-field"
                        x-show="$refs.adaCheckbox && $refs.adaCheckbox.checked"
                    />
                </div>
            </div>
        </div>
</x-card>
