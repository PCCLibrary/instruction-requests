{{-- /views/instruction-requests/partials/create/instruction-type.blade.php --}}
<x-fieldset legend="Instruction Type" classes="bg-white">
    <div class="space-y-6">
        {{-- Instruction Type Selector --}}
        <div class="grid grid-cols-1 gap-6">
            <x-input-select
                name="instruction_type"
                id="instruction_type"
                label="Instruction Type"
                :options="[
                    'on-campus' => 'Librarian joins my class on campus',
                    'remote' => 'Librarian joins my online (scheduled meeting) class',
                    'asynchronous' => 'Librarian provides resources to be used asynchronously'
                ]"
                :selected="old('instruction_type')"
                help-text="Please select what you need help with."
                required
                x-on:change="instructionType = $event.target.value"
            />
        </div>
    </div>
</x-fieldset>
