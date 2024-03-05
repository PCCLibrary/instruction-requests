{{--Build array for checkboxes--}}
{!! Form::hidden('instruction_request_id', $instructionRequestDetails->instruction_request_id) !!}
{!! Form::hidden('librarian_id', $instructionRequestDetails->librarian_id) !!}
{!! Form::hidden('created_by', $instructionRequestDetails->created_by) !!}
{!! Form::hidden('last_updated_by',  auth()->user()->display_name ) !!}

{{--<pre>{{ print_r(json_encode($instructionRequestDetails), true) }}</pre>--}}

{{--<x-input-checkbox-group--}}
{{--    name="tasks_completed"--}}
{{--    label="Tasks Completed"--}}
{{--    :checkboxes="$checkboxValues"--}}
{{--/>--}}

<x-input-checkbox
    name="research_guide"
    label="Research Guide"
    :value="$instructionRequestDetails->research_guide"
    />

<x-input-checkbox
    name="video"
    label="Video"
    :value="$instructionRequestDetails->video"
/>

<x-input-checkbox
    name="embedded"
    label="Embedded"
    :value="$instructionRequestDetails->embedded"
/>

<x-input-checkbox
    name="other"
    label="Other"
    :value="$instructionRequestDetails->other"
/>
<!-- Instruction Duration (Text Input) -->
<x-input-text
    name="instruction_duration"
    label="Instruction Duration"
    :value="$instructionRequestDetails->instruction_duration ?? null"
/>

<!-- Class Notes (Textarea) -->
<x-textarea
    name="class_notes"
    label="Class Notes"
    :value="$instructionRequestDetails->class_notes ?? null"
/>

<!-- Materials (File Upload) -->
<x-input-file
    name="materials"
    label="Materials (File Upload)"
    :multiple="true"
/>

<!-- Assessment Notes (Textarea) -->
<x-textarea
    name="assessment_notes"
    label="Assessment Notes"
    :value="$instructionRequestDetails->assessment_notes ?? null"
/>

<!-- Assessments (File Upload) -->
<x-input-file
    name="assessments"
    label="Assessments (File Upload)"
    :multiple="true"
/>

<!-- Submit Button -->
<div class="form-group">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{{ route('instructionRequests.index') }}" class="btn btn-default">Cancel</a>
</div>
