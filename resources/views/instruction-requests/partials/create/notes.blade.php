{{-- /views/instruction-requests/partials/create/notes.blade.php --}}
<x-fieldset legend="Notes" classes="bg-white on-campus-fields remote-fields asynchronous-fields">
    <div class="space-y-4">
        <x-input-textarea
            name="class_notes"
            label="Class Notes"
            :value="old('class_notes')"
            help-text="Any additional notes about the class"
        />

        <x-input-textarea
            name="assessment_notes"
            label="Assessment Notes"
            :value="old('assessment_notes')"
            help-text="Notes about assessment requirements or expectations"
        />
    </div>
</x-fieldset>
