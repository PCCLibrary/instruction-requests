{{-- resources/views/instruction-requests/partials/create/ada-provisions.blade.php --}}
<x-card title="ADA Provisions" class="bg-gray-50 mb-4">
        <div class="space-y-4">
            <div class="flex items-start space-x-4">
                <div class="w-1/3">
                    <x-input-checkbox
                        name="ada_provisions_needed"
                        label="ADA Provisions Needed"
                        :checked="old('ada_provisions_needed')"
                    />
                </div>
                <div class="w-2/3">
                    <x-input-textarea
                        name="ada_provisions_description"
                        label="Describe the ADA accommodations needed for your class"
                        :value="old('ada_provisions_description')"
                        help-text="Please provide details about any required accommodations"
                    />
                </div>
            </div>
        </div>
</x-card>
