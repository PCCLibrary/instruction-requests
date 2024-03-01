<div class="col-12 row">
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
                  helptext='Preferred pronouns'
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
</div>

<div class="col-6">
    {{-- Instruction Type Field --}}
    <x-input-select name="instruction_type"
                    label="Instruction Type"
                    :options="['In-Person' => 'In-Person', 'Online' => 'Online', 'Hybrid' => 'Hybrid']"
                    :selected="old('instruction_type')"
    />
</div>

<div class="col-6">
    {{-- Course Modality Field --}}
    <x-input-select name="course_modality"
                    label="Instruction Mode"
                    :options="['Synchronous' => 'Synchronous', 'Asynchronous' => 'Asynchronous', 'I\'m not sure' => 'I\'m not sure']"
                    :selected="old('course_modality')"
                    helptext="Examples of asynchronous instruction include the development of tutorials, videos, research guides, having a librarian embedded in your Brightspace classroom to support students, and more."
    />
</div>

<div class="col-6">
    {{--     Librarian Preference Field--}}
    <x-input-select name="librarian_id"
                    label="Librarian Preference"
                    :options="$librarians->toArray()"
{{--                    :selected="old('librarian_id')"--}}
    />
</div>

<div class="col-6">
    {{--     Campus ID Field--}}
    <x-input-select name="campus_id"
                    label="Campus"
                    :options="$campuses->toArray()"
{{--                    :selected="old('campus_id')"--}}
    />
</div>

<div class="col-3">
{{--         Department Field--}}
    <x-input-select name="department"
                    label="Subject"
                    :options="$departments"
{{--                    :selected="old('department')"--}}
    />
</div>

<div class="col-3">
    {{-- Course Number Field --}}
    <x-input-text name="course_number"
                  label="Course Number"
                  :value="old('course_number')"
    />
</div>

<div class="col-3">
    {{-- Course CRN Field --}}
    <x-input-text name="course_crn"
                  label="Course CRN"
                  :value="old('course_crn')"
    />
</div>

<div class="col-3">
    {{-- Number of Students Field --}}
    <x-input-text name="number_of_students"
                  label="Number of Students"
                  type="number"
                  :value="old('number_of_students')"
    />
</div>

<div class="col-3">
    {{-- ADA Provisions Needed Field --}}
    <x-checkbox name="ada_provisions_needed"
                label="ADA Provisions Needed"
                :checked="old('ada_provisions_needed', false)"
    />
</div>

<div class="col-9">
    {{-- ADA Provisions Description Field --}}
    <x-textarea name="ada_provisions_description"
                label="ADA Provisions Description"
                :value="old('ada_provisions_description')"
                helptext="Describe the ADA accommodations needed for your class."
    />
</div>

<div class="col-3">
    {{-- Preferred Datetime Field --}}
    <x-input-datetime name="preferred_datetime"
                      label="Preferred Datetime"
                      :value="old('preferred_datetime')"
                      helptext="Enter the date and time you would prefer to have your instruction session."
    />
</div>

<div class="col-3">
    {{-- Alternate Datetime Field --}}
    <x-input-datetime name="alternate_datetime"
                      label="Alternate Datetime"
                      :value="old('alternate_datetime')"
                      helptext="Enter an alternate date and time for your instruction session."
    />
</div>

<div class="col-3">
    {{-- Duration Field --}}
    <x-input-text name="duration"
                  label="Duration"
                  :value="old('duration')"
                  helptext="Enter the length of instruction you would like your class to receive, in minutes."
    />
</div>

<div class="col-3">
    {{-- Asynchronous Instruction Ready Date Field --}}
    <x-input-date name="asynchronous_instruction_ready_date"
                  label="Asynchronous Instruction Ready Date"
                  :value="old('asynchronous_instruction_ready_date')"
    />
</div>

<div class="col-3">
    {{-- Selected Extra Time --}}
    <x-checkbox name="need_extra_time"
                label="I need extra time with class"
                :checked="old('need_extra_time', false)"
    />
</div>

<div class="col-9">
    {{-- Extra Time With Class Field --}}
    <x-textarea name="extra_time_with_class"
                label="Extra Time With Class"
                :value="old('extra_time_with_class')"
    />
</div>

<div class="col-12">
    <p class="text-bold">Preparation Status</p>
    {{-- Received Assignment Field --}}
    <x-checkbox name="received_assignment"
                label="Received Assignment"
                :checked="old('received_assignment', false)"
    />

    {{-- Selected Topics Field --}}
    <x-checkbox name="selected_topics"
                label="Selected Topics"
                :checked="old('selected_topics', false)"
    />

    {{-- Explored Background Field --}}
    <x-checkbox name="explored_background"
                label="Explored Background"
                :checked="old('explored_background', false)"
    />

    {{-- Written Draft Field --}}
    <x-checkbox name="written_draft"
                label="Written Draft"
                :checked="old('written_draft', false)"
    />

    {{-- Other Learning Outcome Field --}}
    <x-checkbox name="other_learning_outcome"
                label="Other Learning Outcome"
                :checked="old('other_learning_outcome', false)"
    />
</div>

<div class="col-4">
    {{-- Other Learning Outcome Description Field --}}
    <x-textarea name="other_learning_outcome_description"
                label="Learning Outcomes"
                :value="old('other_learning_outcome_description')"
    />
</div>
