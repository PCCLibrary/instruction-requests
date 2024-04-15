<!-- Class Notes (Textarea) -->
<x-textarea
    name="class_notes"
    label="Class Notes"
    :value="$instructionRequest->detail->class_notes ?? null"
    classes="mb-4"
/>

<!-- Assessment Notes (Textarea) -->
<x-textarea
    name="assessment_notes"
    label="Assessment Notes"
    :value="$instructionRequest->detail->assessment_notes ?? null"
    classes="mb-4"
/>
