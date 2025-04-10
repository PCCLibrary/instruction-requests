{{-- ADA Provisions Section --}}
<x-card title="ADA Provisions" class="bg-gray-50 mb-4">
            <div class="space-y-4" x-data="{
                get isDisabled() {
                    return !$store.formState.isSectionEditable('adaProvisions');
                }
            }">
                <div class="flex items-start space-x-4">
                    <div class="w-1/3">
                        <x-input-checkbox
                            name="ada_provisions_needed"
                            label="ADA Provisions Needed"
                            :checked="$instructionRequest->ada_provisions_needed"
                            class="edit-field"
                            x-bind:class="isDisabled ? 'opacity-50 cursor-not-allowed' : ''"
                        />
                    </div>
                    <div class="w-2/3">
                        <x-input-textarea
                            name="ada_provisions_description"
                            label="Describe the ADA accommodations needed for your class"
                            :value="$instructionRequest->ada_provisions_description"
                            class="edit-field"
                            x-bind:readonly="isDisabled"
                            x-bind:class="isDisabled ? 'bg-gray-100 cursor-not-allowed' : ''"
                        />
                    </div>
                </div>
            </div>
</x-card>
