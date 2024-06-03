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
    {{-- Preferred Datetime Field --}}
    <x-input-datetime name="preferred_datetime"
                      label="Preferred Datetime"
                      :value="old('preferred_datetime', $instructionRequest->preferred_datetime ?? null)"
                      classes="col-3"
    />

    {{-- Alternate Datetime Field --}}
    <x-input-datetime name="alternate_datetime"
                      label="Alternate Datetime"
                      :value="old('alternate_datetime', $instructionRequest->alternate_datetime ?? null)"
                      classes="col-3"

    />

    {{-- Duration Field --}}
    <x-input-text name="duration"
                  label="Duration"
                  :value="old('duration', $instructionRequest->duration ?? null)"
                  classes="col-3"
                  helptext="Duration in minutes."
    />


    {{-- Asynchronous Instruction Ready Date Field --}}
    <x-input-date name="asynchronous_instruction_ready_date"
                  label="Ready Date"
                  :value="old('asynchronous_instruction_ready_date', $instructionRequest->asynchronous_instruction_ready_date ?? null)"
                  classes="col-3"
    />
</x-row>

<x-row>
    {{-- Instruction Type Field --}}
    <x-input-select name="instruction_type"
                    label="Instruction Type"
                    :options="[
            'on-campus' => 'Librarian joins my class on campus',
            'remote' => 'Librarian joins my remote class',
            'asynchronous' => 'Librarian provides resources to be used asynchronously']"
                    :selected="old('instruction_type', $instructionRequest->instruction_type ?? null)"
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
    {{-- Class Description --}}
    <x-textarea name="class_description"
                label="Class description"
                :value="old('class_description', $instructionRequest->class_description ?? null)"
{{--                helptext="Describe the ADA accommodations needed for your class."--}}
                classes="col-8"
    />
</x-row>

<x-row>
    {{-- ADA Provisions Needed Field --}}
    <x-input-checkbox name="ada_provisions_needed"
                label="ADA Provisions Needed"
                      :checked="old('ada_provisions_needed', $instructionRequest->ada_provisions_needed ?? null)"                classes="col-4"
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
                :value="old('other_learning_outcome_description', $instructionRequest->other_learning_outcome_description ?? null)"
    />
</div>
</x-row>

<x-row>
    <x-textarea name="library_instruction_description"
                label="What do you want your students to get out of library instruction?"
                :value="old('other_learning_outcome_description', $instructionRequest->library_instruction_description ?? null)"
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
