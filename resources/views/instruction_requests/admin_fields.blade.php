{{--Build array for checkboxes--}}
{{--{!! Form::hidden('instruction_request_id', $instructionRequest->detail->id) !!}--}}
{!! Form::hidden('librarian_id', $instructionRequest->librarian_id) !!}
{!! Form::hidden('created_by', $instructionRequest->detail->created_by ) !!}
{!! Form::hidden('last_updated_by',  auth()->user()->display_name ) !!}

{{--<pre>{{ print_r(json_encode($instructionRequest->detail), true) }}</pre>--}}

{{--<pre>{{ print_r($checkboxValues, true) }}</pre>--}}
{{--$instructionRequest->detail--}}

<ul class="list-unstyled mb-4">
    <li>
        <x-input-checkbox
            name="research_guide"
            label="Research Guide"
            :value="$instructionRequest->detail->research_guide"
    />
    </li>

    <li>
        <x-input-checkbox
            name="video"
            label="Video"
            :value="$instructionRequest->detail->video"
    />
    </li>

    <li>
    <x-input-checkbox
        name="embedded"
        label="Embedded"
        :value="$instructionRequest->detail->embedded"
    />
    </li>
    <li>
    <x-input-checkbox
        name="other"
        label="Other"
        :value="$instructionRequest->detail->other"
    />
    </li>
</ul>
<!-- Instruction Duration (Text Input) -->
<x-input-text
    name="instruction_duration"
    label="Instruction Duration"
    :value="$instructionRequest->detail->instruction_duration ?? null"
    classes="mb-4"
/>

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

<!-- Materials (File Upload) -->
<x-input-file
    name="materials"
    label="Materials (File Upload)"
    :multiple="true"
    classes="mb-4"
/>

<!-- Assessments (File Upload) -->
<x-input-file
    name="assessments"
    label="Assessments (File Upload)"
    :multiple="true"
    classes="mb-4"
/>