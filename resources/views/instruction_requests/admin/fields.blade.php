{{--Build array for checkboxes--}}
{!! Form::hidden('assigned_librarian_id', $instructionRequest->assigned_librarian_id) !!}
{!! Form::hidden('created_by', $instructionRequest->detail->created_by ) !!}
{!! Form::hidden('last_updated_by',  auth()->user()->display_name ) !!}

<x-input-select name="status"
                label="Request Status"
                :options="['received' => 'Received', 'assigned' => 'Assigned', 'accepted' => 'Accepted', 'completed' => 'Completed']"
                :selected="old('status', $instructionRequest->status ?? null)"
                classes="mb-4"
/>


<x-input-select name="assigned_librarian_id"
                label="Assigned Librarian"
                :options="$librarians->pluck('display_name', 'id')->toArray()"
                :selected="$instructionRequest->detail->assigned_librarian_id"
                classes="mb-4"
/>


{{-- Preferred Datetime Field --}}
<x-input-datetime name="instruction_datetime"
                  label="Instruction Date & Time"
                  :value="$instructionRequest->detail->instruction_datetime ?? $instructionRequest->preferred_datetime"
                  required=true
/>

<!-- Instruction Duration (Text Input) -->
<x-input-duration
    name="instruction_duration"
    label="Instruction Duration"
    :value="$instructionRequest->detail->instruction_duration ?? $instructionRequest->duration"
/>

@include('instruction_requests.admin.tasks')

{{--<ul class="list-unstyled mb-4">--}}
{{--    <li class="mb-2"><strong>Tasks Completed</strong></li>--}}
{{--    <li>--}}
{{--        <x-input-checkbox--}}
{{--            name="video"--}}
{{--            label="Video"--}}
{{--            :checked="$instructionRequest->detail->video"--}}
{{--        />--}}
{{--    </li>--}}
{{--    <li>--}}
{{--        <x-input-checkbox--}}
{{--            name="non_video"--}}
{{--            label="Non video"--}}
{{--            :checked="$instructionRequest->detail->non_video"--}}
{{--        />--}}
{{--    </li>--}}
{{--    <li>--}}
{{--        <x-input-checkbox--}}
{{--            name="modified_tutorial"--}}
{{--            label="Modified Tutorial"--}}
{{--            :checked="$instructionRequest->detail->modified_tutorial"--}}
{{--        />--}}
{{--    </li>--}}
{{--    <li>--}}
{{--        <x-input-checkbox--}}
{{--            name="embedded"--}}
{{--            label="Embedded Librarian"--}}
{{--            :checked="$instructionRequest->detail->embedded"--}}
{{--        />--}}
{{--    </li>--}}

{{--    <li>--}}
{{--        <x-input-checkbox--}}
{{--            name="research_guide"--}}
{{--            label="Research Guide"--}}
{{--            :checked="$instructionRequest->detail->research_guide"--}}
{{--    />--}}
{{--    </li>--}}

{{--    <li>--}}
{{--        <x-input-checkbox--}}
{{--            name="handout"--}}
{{--            label="Handout"--}}
{{--            :checked="$instructionRequest->detail->handout"--}}
{{--    />--}}
{{--    </li>--}}

{{--    <li>--}}
{{--    <x-input-checkbox--}}
{{--        name="developed_assigment"--}}
{{--        label="Developed Assignment"--}}
{{--        :checked="$instructionRequest->detail->developed_assignment"--}}
{{--    />--}}
{{--    </li>--}}
{{--    <li>--}}
{{--    <x-input-checkbox--}}
{{--        name="other_materials"--}}
{{--        label="Other"--}}
{{--        :checked="$instructionRequest->detail->other_materials"--}}
{{--    />--}}
{{--    </li>--}}
{{--    <li>--}}
{{--        <x-textarea--}}
{{--            name="other_describe"--}}
{{--            label="Describe Other"--}}
{{--            :value="$instructionRequest->detail->other_describe"--}}
{{--            classes="p-0 my-4"--}}
{{--        />--}}
{{--    </li>--}}
{{--</ul>--}}
