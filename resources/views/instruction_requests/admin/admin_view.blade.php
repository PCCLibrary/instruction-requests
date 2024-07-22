{{-- Contact Information --}}
<x-card title="Contact Information" headerclass="bg-info">
    <x-slot name="body">
        <table class="table mb-0 ">
            <thead>
            <tr class="bg-dark text-white">
                <th>Instructor</th>
                <th>Preferred Name</th>
                <th>Pronouns</th>
                <th>Email</th>
                <th>Edit</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>{{ $instructionRequest->instructor->name }}</td>
                <td>{{ $instructionRequest->instructor->display_name }}</td>
                <td>{{ $instructionRequest->instructor->pronouns }}</td>
                <td>{{ $instructionRequest->instructor->email }}</td>
                <td>
                    <a href="{{ route('instructors.edit', $instructionRequest->instructor->id) }}"
                       class='btn btn-success btn-xs'
                       target="_blank"
                       title="Click to edit the instructor information"
                    >
                        <i class="fa fa-edit"></i>
                    </a>
                </td>
            </tr>
            </tbody>
        </table>
    </x-slot>
</x-card>

{{-- Class Information --}}
<x-card title="Request Information" headerclass="bg-info">

    <x-slot name="body">

    <input type="hidden" name="instruction_type" value="{{ $instructionRequest->instruction_type ?? null }}">
    <input type="hidden" name="department" value="{{ $instructionRequest->department ?? null }}">
    <input type="hidden" name="course_number" value="{{ $instructionRequest->course_number ?? null }}">
    <input type="hidden" name="course_crn" value="{{ $instructionRequest->course_crn ?? null }}">
    <input type="hidden" name="number_of_students" value="{{ $instructionRequest->number_of_students ?? null }}">


        <x-fieldset legend="" classes="card-body">

            <button id="toggleEditButton" class="btn btn-success btn-xs"><i class="fa fa-edit"></i></button>

            <x-row>

                {{-- Instruction Type Field --}}
                <x-input-select name="instruction_type"
                                label="Instruction Type"
                                :options="[
            'on-campus' => 'Librarian joins my class on campus',
            'remote' => 'Librarian joins my remote class',
            'asynchronous' => 'Librarian provides resources to be used asynchronously']"
                                :selected="$instructionRequest->instruction_type ?? null "
                                classes="col-lg-9 edit-field"
                                tophelptext="Please select what you need help with."
                                required=false
                                disabled="true"
                />

            </x-row>

            <x-row>
                {{-- Department Field --}}
                <x-input-select name="department"
                                label="Subject"
                                :options="$departments"
                                :selected=" $instructionRequest->department ?? null "
                                classes="col-lg-6 edit-field"
                                helptext="Choose the subject of your course."
                                required=false
                                disabled="true"
                />

                {{-- Course Number Field --}}
                <x-input-text name="course_number"
                              label="Course Number"
                              :value="$instructionRequest->course_number ?? '' "
                              classes="col-lg-6 edit-field"
                              helptext='Enter the course number (e.g., "122" for course BI 122). Enter "0000" if the course has no number.'
                              required=false
                              disabled="true"
                />

            </x-row>

            <x-row>

                {{-- Course CRN Field --}}
                <x-input-text name="course_crn"
                              label="Course CRN"
                              :value="$instructionRequest->course_crn ?? '' "
                              classes="col-lg-6 edit-field"
                              helptext="Enter the 5-digit CRN for your course. Enter 99999 if the course has no CRN."
                              required=false
                              disabled="true"
                />

                {{-- Number of Students Field --}}
                <x-input-text name="number_of_students"
                              label="Number of Students"
                              type="number"
                              :value="$instructionRequest->number_of_students ?? '' "
                              classes="col-lg-6 edit-field"
                              helptext="Enter the number of students in the class."
                              required=false
                              disabled="true"
                />

            </x-row>
        </x-fieldset>

    </x-slot>

    <x-slot name="footer">

        @if(isset($instructionRequest->librarian->display_name) || isset($instructionRequest->campus->name))

            <div class="row mb-4">

                @if($instructionRequest->librarian->display_name)
                <div class="col-6">
                    <strong>Requested librarian:</strong>
                    <p>{{ $instructionRequest->librarian->display_name }}</p>
                </div>
                @endif

                @if($instructionRequest->librarian->display_name)
                    <div class="col-6">
                        <strong>Campus:</strong>
                        <p>{{ $instructionRequest->campus->name }}</p>
                    </div>
                @endif
            </div>

        @endif


{{-- Date & time --}}

    @if($instructionRequest->instruction_type !== 'asynchronous')
{{--        <strong>Date, Time, and Duration</strong>--}}
            <div class="row">

                <div class="col-4 mb-4">
                        <strong>Preferred Date & Time</strong>
                            <p>{{ \Carbon\Carbon::parse($instructionRequest->preferred_datetime)->format('M d, Y g:i A') }}</p>
                </div>

                <div class="col-4 mb-4">
                        <strong>Alternate Date & Time</strong>
                            <p>{{ \Carbon\Carbon::parse($instructionRequest->alternate_datetime)->format('M d, Y g:i A') }}</p>
                </div>

                <div class="col-4 mb-4">
                        <strong>Duration</strong>
                        <p>{{ $instructionRequest->duration }}</p>
                </div>

            </div>

        @elseif($instructionRequest->instruction_type == 'asynchronous')
        <div class="row">
            <div class="col">
                <strong>Asynchronous Instruction Ready By:</strong>
                <p>{{ \Carbon\Carbon::parse($instructionRequest->asynchronous_instruction_ready_date)->format('F d, Y') }}</p>
            </div>
        </div>
    @endif

    <div class="row">
        @if(!empty($instructionRequest->extra_time_with_class))
            <div class="col-4">
                <strong>Extra time needed for:</strong>
                <p>{{ $instructionRequest->extra_time_with_class }}</p>
            </div>
        @endif

        @if(!empty($instructionRequest->class_description))
            <div class="col-4">
                <strong>Additional Class Notes:</strong>
                <p>{{ $instructionRequest->class_description }}</p>
            </div>
        @endif

        @if($instructionRequest->ada_provisions_needed && !empty($instructionRequest->ada_provisions_description))
        <div class="col-4">
            <strong>ADA Provisions Description:</strong>
            <p>{{ $instructionRequest->ada_provisions_description }}</p>
        </div>
        @endif
    </div>
    </x-slot>
</x-card>

<input name="instruction_request_id" type="hidden" value="{{ $instructionRequest->detail->instruction_request_id }}">
<input name="instruction_type" type="hidden" value="{{ $instructionRequest->instruction_type  }}" />

<input name="instruction_type" type="hidden" value="{{ $instructionRequest->instruction_type  }}" />
<input name="instructor_id" type="hidden" value="{{ $instructionRequest->instructor_id  }}" />

<input name="librarian_id" type="hidden" value="{{ $instructionRequest->librarian_id ?? 2 }}" />
<input name="campus_id" type="hidden" value="{{ $instructionRequest->campus_id ?? 1 }}" />

<input type="hidden" name="preferred_datetime" value="{{ $instructionRequest->preferred_datetime ?? null }}">
<input type="hidden" name="alternate_datetime" value="{{ $instructionRequest->alternate_datetime ?? null }}">
<input type="hidden" name="duration" value="{{ $instructionRequest->duration ?? null }}">
<input type="hidden" name="asynchronous_instruction_ready_date" value="{{ $instructionRequest->asynchronous_instruction_ready_date ?? null }}">

@php
    $shouldDisplay = $instructionRequest->instruction_type !== 'asynchronous' && (
        $instructionRequest->received_assignment ||
        $instructionRequest->selected_topics ||
        $instructionRequest->explored_background ||
        $instructionRequest->written_draft ||
        $instructionRequest->other_learning_outcome
    );
@endphp

@if($shouldDisplay)

            <x-card title="Students Have:" headerclass="bg-info">
                <x-slot name="body">
                    <table class="table thead-dark">
                        <thead>
                        <tr>
                            <th>Received Assignment</th>
                            <th>Selected Topics</th>
                            <th>Explored Background</th>
                            <th>Written Draft</th>
                            <th>Other Learning Outcome</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><strong>{!! $instructionRequest->received_assignment ? '&#x2713;' : '' !!}</strong></td>
                            <td><strong>{!! $instructionRequest->selected_topics ? '&#x2713;' : '' !!}</strong></td>
                            <td><strong>{!! $instructionRequest->explored_background ? '&#x2713;' : '' !!}</strong></td>
                            <td><strong>{!! $instructionRequest->written_draft ? '&#x2713;' : '' !!}</strong></td>
                            <td><strong>{!! $instructionRequest->other_learning_outcome ? '&#x2713;' : '' !!}</strong></td>
                        </tr>
                        </tbody>
                    </table>
                    @if(!empty($instructionRequest->other_learning_outcome_description))
                        <x-slot name="footer">
                            <strong>Other Learning Outcome Description:</strong>
                            <p>{{ $instructionRequest->other_learning_outcome_description }}</p>
                        </x-slot>
                    @endif
                </x-slot>
            </x-card>

@endif


@if(!empty($instructionRequest->library_instruction_description) || !empty($instructionRequest->genai_discussion_interest))
        <x-card title="Other Information" headerclass="bg-info">
            @if(!empty($instructionRequest->library_instruction_description))
                    <div class="mb-4">
                        <strong>Instruction Goals:</strong>
                        <p>{{ $instructionRequest->library_instruction_description }}</p>
                    </div>
            @endif
            @if(!empty($instructionRequest->genai_discussion_interest))
                    <div class="mb-4">
                        <strong>GenAI Discussion Interest:</strong>
                        <p>{{ $instructionRequest->genai_discussion_interest }}</p>
                    </div>
            @endif
        </x-card>
@endif
