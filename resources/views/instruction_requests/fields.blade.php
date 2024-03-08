{{-- Check if we are editing an existing instruction request --}}
@isset($instructionRequest->id)

    <x-card classes="bg-gray-500">
   <x-row>
        <div class="col-6">

            <ul class="list-unstyled">
                <li><h3 class="mb-2">Instructor</h3></li>
                <li><a href="{{ route('instructors.edit', $instructionRequest->instructor_id) }}" title="Click to edit instructor information"><i class="fa fa-edit"></i> {{ $instructionRequest->instructor->display_name }}</a></li>
                <li><a href="mailto:{{ $instructionRequest->instructor->email }}" title="Click to email instructor"><i class="fa fa-envelope"></i> {{ $instructionRequest->instructor->email }}</a></li>
            </ul>
        </div>

       <div class="col-3">
           <ul class="list-unstyled">
               <li><label class="mr-4">Placeholder for syllabus</label>
               </li>
            <li class="text-blue"><i class="fa fa-file"></i> Syllabus</li>
           </ul>
       </div>

       <div class="col-3">
           <ul class="list-unstyled">
               <li><label>Placeholder for assignments</label></li>
               <li class="text-blue"><i class="fa fa-file"></i> Assignment 1</li>
               <li class="text-blue"><i class="fa fa-file"></i> Assignment 2</li>
               <li class="text-blue"><i class="fa fa-file"></i> Assignment 3</li>
           </ul>

       </div>

   </x-row>

    <x-row classes="row py-0 my-0">
       <x-textarea name="assignment_description"
                   label="Assignment Description"
                   :value="old('assignment_description', $instructionRequest->assignment_description ?? null)"
                   classes="col-12"
       />
   </x-row>
    </x-card>

       {!! Form::hidden('instruction_request_id', $instructionRequest->id) !!}
       {!! Form::hidden('instructor_id', $instructionRequest->instructor_id) !!}

@else
    <x-row>
        <h3 class="col-12 mb-4">Contact Information</h3>
        {{-- Instructor Name --}}
        <x-input-text name="name"
                      label="Name"
                      :value="old('name')"
                      helptext="Instructor name"
                      classes="col-6"
                      required=true
        />

        {{-- Display Name --}}
        <x-input-text name="display_name"
                      label="Display Name"
                      :value="old('display_name')"
                      helptext='"Students refer to me as"'
                      classes="col-6"
        />

        {{-- Pronouns --}}
        <x-input-text name="pronouns"
                      label="Pronouns"
                      :value="old('pronouns')"
                      classes="col-4"
{{--                      helptext='Preferred pronouns'--}}

        />

        {{-- Email --}}
        <x-input-text name="email"
                      label="Email"
                      :value="old('email')"
                      classes="col-4"
        />

        {{-- Phone --}}
        <x-input-text name="phone"
                      label="Phone"
                      :value="old('phone')"
                      classes="col-4"
        />
    </x-row>
@endisset


<x-row>
    <h3 class="col-12 mb-4">Instruction Request Information</h3>
    {{-- Instruction Type Field --}}
    <x-input-select name="instruction_type"
                    label="Instruction Type"
                    :options="['In-Person' => 'In-Person', 'Online' => 'Online', 'Hybrid' => 'Hybrid']"
                    :selected="old('instruction_type', $instructionRequest->instruction_type ?? null)"
                    classes="col-6"
    />

    {{-- Course Modality Field --}}
    <x-input-select name="course_modality"
                    label="Instruction Mode"
                    :options="['Synchronous' => 'Synchronous', 'Asynchronous' => 'Asynchronous', 'I\'m not sure' => 'I\'m not sure']"
                    :selected="old('course_modality', $instructionRequest->course_modality ?? null)"
                    helptext="Examples of asynchronous instruction include the development of tutorials, videos, research guides, having a librarian embedded in your Brightspace classroom to support students, and more."
                    classes="col-6"
    />
</x-row>

<x-row>
{{--     Librarian Preference Field--}}
    <x-input-select name="librarian_id"
                    label="Librarian Preference"
                    :options="$librarians->pluck('display_name', 'id')->toArray()"
                    :selected="old('librarian_id', $instructionRequest->librarian_id ?? null)"
                    classes="col-6"
    />

{{--     Campus ID Field--}}
    <x-input-select name="campus_id"
                    label="Campus"
                    :options="$campuses->pluck('name', 'id')"
                    :selected="old('campus_id', $instructionRequest->campus_id ?? null)"
                    classes="col-6"
    />

</x-row>

<x-row>
{{--     Department Field--}}
    <x-input-select name="department"
                    label="Subject"
                    :options="$departments"
                    :selected="old('department', $instructionRequest->department ?? null)"
                    classes="col-5"

    />

    {{-- Course Number Field --}}
    <x-input-text name="course_number"
                  label="Course Number"
                  :value="old('course_number', $instructionRequest->course_number ?? null)"
                  classes="col-2"

    />

    {{-- Course CRN Field --}}
    <x-input-text name="course_crn"
                  label="Course CRN"
                  :value="old('course_crn', $instructionRequest->course_crn ?? null)"
                  classes="col-2"
    />

    {{-- Number of Students Field --}}
    <x-input-text name="number_of_students"
                  label="Number of Students"
                  type="number"
                  :value="old('number_of_students', $instructionRequest->number_of_students ?? null)"
                  classes="col-3"

    />
</x-row>

<x-row>
    {{-- ADA Provisions Needed Field --}}
    <x-input-checkbox name="ada_provisions_needed"
                label="ADA Provisions Needed"
                :checked="$instructionRequest->ada_provisions_needed"
                classes="col-4"
    />
{{--    {{ $instructionRequest->ada_provisions_needed }}--}}

    {{-- ADA Provisions Description Field --}}
    <x-textarea name="ada_provisions_description"
                label="ADA Provisions Description"
                :value="old('ada_provisions_description', $instructionRequest->ada_provisions_description ?? null)"
                helptext="Describe the ADA accommodations needed for your class."
                classes="col-8"
    />
</x-row>

<x-row>
    {{-- Preferred Datetime Field --}}
    <x-input-datetime name="preferred_datetime"
                      label="Preferred Datetime"
                      :value="old('preferred_datetime', $instructionRequest->preferred_datetime ?? null)"
                      helptext="Enter the date and time you would prefer to have your instruction session."
                      classes="col-3"
    />

    {{-- Alternate Datetime Field --}}
    <x-input-datetime name="alternate_datetime"
                      label="Alternate Datetime"
                      :value="old('alternate_datetime', $instructionRequest->alternate_datetime ?? null)"
                      helptext="Enter an alternate date and time for your instruction session."
                      classes="col-3"

    />

    {{-- Duration Field --}}
    <x-input-text name="duration"
                    label="Duration"
                    :value="old('duration', $instructionRequest->duration ?? null)"
                    helptext="Enter the length of instruction you would like your class to receive, in minutes."
                    classes="col-3"
    />


    {{-- Asynchronous Instruction Ready Date Field --}}
    <x-input-date name="asynchronous_instruction_ready_date"
                  label="Ready Date"
                  :value="old('asynchronous_instruction_ready_date', $instructionRequest->asynchronous_instruction_ready_date ?? null)"
                  classes="col-3"
                  helptext="Asynchronous instruction ready date."
    />
</x-row>



<x-row>
    {{-- Extra Time With Class Field --}}
    <x-textarea name="extra_time_with_class"
                label="Extra Time With Class"
                :value="old('extra_time_with_class', $instructionRequest->extra_time_with_class ?? null)"
                classes="col-9"
    />
</x-row>

<x-row>
<div class="col-3">
    <p class="text-bold">Preparation Status</p>
    {{-- Received Assignment Field --}}
    <x-input-checkbox name="received_assignment"
                label="Received Assignment"
                :checked="old('received_assignment', $instructionRequest->received_assignment ?? false)"
    />

    {{-- Selected Topics Field --}}
    <x-input-checkbox name="selected_topics"
                label="Selected Topics"
                :checked="old('selected_topics', $instructionRequest->selected_topics ?? false)"
    />

    {{-- Explored Background Field --}}
    <x-input-checkbox name="explored_background"
                label="Explored Background"
                :checked="old('explored_background', $instructionRequest->explored_background ?? false)"
    />

    {{-- Written Draft Field --}}
    <x-input-checkbox name="written_draft"
                label="Written Draft"
                :checked="old('written_draft', $instructionRequest->written_draft ?? false)"
    />

    {{-- Other Learning Outcome Field --}}
    <x-input-checkbox name="other_learning_outcome"
                label="Other Learning Outcome"
                :checked="old('other_learning_outcome', $instructionRequest->other_learning_outcome ?? false)"
    />
</div>

<div class="col-9">
    {{-- Other Learning Outcome Description Field --}}
    <x-textarea name="other_learning_outcome_description"
                label="Learning Outcomes"
                :value="old('other_learning_outcome_description', $instructionRequest->other_learning_outcome_description ?? '')"
    />
</div>
</x-row>

<x-row>
    <x-textarea name="library_instruction_description"
                label="What do you want your students to get out of library instruction?"
                :value="old('other_learning_outcome_description', $instructionRequest->library_instruction_description ?? '')"
                classes="col-9"
    />
</x-row>

<x-row>

    {{-- genai_discussion_interest Field --}}
    <x-textarea name="genai_discussion_interest"
                label="Generative AI"
                :value="old('genai_discussion_interest', $instructionRequest->genai_discussion_interest ?? null)"
                classes="col-9"
    />

</x-row>
