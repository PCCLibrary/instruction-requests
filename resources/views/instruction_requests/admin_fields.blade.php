{{--Build array for checkboxes--}}
{{--{!! Form::hidden('instruction_request_id', $instructionRequest->detail->id) !!}--}}
{!! Form::hidden('assigned_librarian_id', $instructionRequest->assigned_librarian_id) !!}
{!! Form::hidden('created_by', $instructionRequest->detail->created_by ) !!}
{!! Form::hidden('last_updated_by',  auth()->user()->display_name ) !!}

{{--<pre>{{ print_r(json_encode($instructionRequest->detail), true) }}</pre>--}}

{{--<pre>{{ print_r($checkboxValues, true) }}</pre>--}}
{{--$instructionRequest->detail--}}

<x-input-select name="assigned_librarian_id"
                label="Assigned Librarian"
                :options="$librarians->pluck('display_name', 'id')->toArray()"
                :selected="$instructionRequest->detail->assigned_librarian_id"
                classes="mb-4"
/>



<ul class="list-unstyled mb-4">
    <li class="mb-2"><strong>Tasks Completed</strong></li>
    <li>
        <x-input-checkbox
            name="video"
            label="Video"
            :checked="$instructionRequest->detail->video"
        />
    </li>
    <li>
        <x-input-checkbox
            name="non_video"
            label="Non video"
            :checked="$instructionRequest->detail->non_video"
        />
    </li>
    <li>
        <x-input-checkbox
            name="modified_tutorial"
            label="Modified Tutorial"
            :checked="$instructionRequest->detail->modified_tutorial"
        />
    </li>
    <li>
        <x-input-checkbox
            name="embedded"
            label="Embedded Librarian"
            :checked="$instructionRequest->detail->embedded"
        />
    </li>

    <li>
        <x-input-checkbox
            name="research_guide"
            label="Research Guide"
            :checked="$instructionRequest->detail->research_guide"
    />
    </li>

    <li>
        <x-input-checkbox
            name="handout"
            label="Handout"
            :checked="$instructionRequest->detail->handout"
    />
    </li>

    <li>
    <x-input-checkbox
        name="developed_assigment"
        label="Developed Assignment"
        :checked="$instructionRequest->detail->developed_assignment"
    />
    </li>
    <li>
    <x-input-checkbox
        name="other_materials"
        label="Other"
        :checked="$instructionRequest->detail->other_materials"
    />
    </li>
    <li>
        <x-textarea
            name="other_describe"
            label="Describe Other"
            :value="$instructionRequest->detail->other_describe"
            classes="p-0 my-4"
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
