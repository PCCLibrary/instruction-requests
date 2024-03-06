{{--Build array for checkboxes--}}
{!! Form::hidden('instruction_request_id', $instructionRequestDetails->instruction_request_id) !!}
{!! Form::hidden('assigned_librarian_id', $instructionRequestDetails->assigned_librarian_id) !!}
{!! Form::hidden('created_by', $instructionRequestDetails->created_by) !!}
{!! Form::hidden('last_updated_by',  auth()->user()->display_name ) !!}

<pre>{{ print_r(json_encode($instructionRequestDetails), true) }}</pre>


<x-input-select name="librarian_id"
                label="Librarian Preference"
                :options="$librarians->pluck('display_name', 'id')->toArray()"
                :selected="$instructionRequestDetails->assigned_librarian_id"
                classes="col-12"
/>

{{-- Video Field --}}
<x-input-checkbox name="video"
                  label="Video"
                  :checked="$instructionRequestDetails->video"
                  classes="col-4"
/>

{{-- Non-Video Field --}}
<x-input-checkbox name="non_video"
                  label="Non-Video"
                  :checked="$instructionRequestDetails->non_video"
                  classes="col-4"
/>

{{-- Modified Tutorial Field --}}
<x-input-checkbox name="modified_tutorial"
                  label="Modified Tutorial"
                  :checked="$instructionRequestDetails->modified_tutorial"
                  classes="col-4"
/>

{{-- Embedded Field --}}
<x-input-checkbox name="embedded"
                  label="Embedded"
                  :checked="$instructionRequestDetails->embedded"
                  classes="col-4"
/>

{{-- Research Guide Field --}}
<x-input-checkbox name="research_guide"
                  label="Research Guide"
                  :checked="$instructionRequestDetails->research_guide"
                  classes="col-4"
/>

{{-- Handout Field --}}
<x-input-checkbox name="handout"
                  label="Handout"
                  :checked="$instructionRequestDetails->handout"
                  classes="col-4"
/>

{{-- Developed Assignment Field --}}
<x-input-checkbox name="developed_assignment"
                  label="Developed Assignment"
                  :checked="$instructionRequestDetails->developed_assignment"
                  classes="col-4"
/>

{{-- Other Materials Field --}}
<x-input-checkbox name="other_materials"
                  label="Other Materials"
                  :checked="$instructionRequestDetails->other_materials"
                  classes="col-4"
/>

{{-- Other Describe Field --}}
<div class="col-6">
    <label for="other_describe">Other Describe</label>
    <input type="text"
           class="form-control"
           name="other_describe"
           id="other_describe"
           value="{{ $instructionRequestDetails->other_describe }}"
    />
</div>

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
