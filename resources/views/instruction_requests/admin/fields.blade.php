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

@if($instructionRequest->status == 'accepted' && $instructionRequest->detail->assigned_librarian_id == Auth::user()->id)
 <div class="my-4">
    <x-modal buttonLabel="Create Google Calendar Event">
        <x-slot name="title">Schedule Calendar Event</x-slot>
        <p>Placeholder for the google event booking form.</p>
    </x-modal>
 </div>
@endif

<x-card>
    <p>Open Google Calendar in new window</p>
    <ul class="list-unstyled">
        @foreach($campuses as $campus)
            <li>
                @if($campus->gcal)
                    <a href="{{ $campus->gcal }}" target="_blank">{{ $campus->name }}</a>
                @else
                    {{ $campus->name }}
                @endif
            </li>
        @endforeach
    </ul>

</x-card>
@include('instruction_requests.admin.tasks')
