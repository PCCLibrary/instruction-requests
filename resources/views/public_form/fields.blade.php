<fieldset class="row mb-4">
        <legend class="col-12 mb-4">Contact information</legend>
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
    </fieldset>


<fieldset class="row mb-4">
    <legend class="col-12 mb-4">Instruction request information</legend>
    {{-- Instruction Type Field --}}
    <x-input-select name="instruction_type"
                    label="Instruction Type"
                    :options="['In-Person' => 'In-Person', 'Online' => 'Online', 'Hybrid' => 'Hybrid']"
                    :selected="old('instruction_type')"
                    classes="col-6"
    />

    {{-- Course Modality Field --}}
    <x-input-select name="course_modality"
                    label="Instruction Mode"
                    :options="['Synchronous' => 'Synchronous', 'Asynchronous' => 'Asynchronous', 'I\'m not sure' => 'I\'m not sure']"
                    :selected="old('course_modality')"
                    helptext="Examples of asynchronous instruction include the development of tutorials, videos, research guides, having a librarian embedded in your Brightspace classroom to support students, and more."
                    classes="col-6"
    />
</fieldset>

<fieldset class="row mb-4">
    {{--     Librarian Preference Field--}}
    <x-input-select name="librarian_id"
                    label="Librarian Preference"
                    :options="$librarians->pluck('display_name', 'id')->toArray()"
                    :selected="old('librarian_id')"
                    classes="col-6"
                    helptext="Do you want to work with a specific librarian or a librarian from a specific campus? We will make every effort to assign your preferred librarian, but we can't guarantee their availability."
    />

    {{--     Campus ID Field--}}
    <x-input-select name="campus_id"
                    label="Class Location"
                    :options="$campuses"
                    :selected="old('campus_id')"
                    classes="col-6"
                    helptext="Choose the campus with which you are primarily associated (if online) ?"
    />

</fieldset>

<fieldset class="row mb-4">
    {{--     Department Field--}}
    <x-input-select name="department"
                    label="Subject"
                    :options="$departments"
                    :selected="old('department')"
                    classes="col-5"
                    helptext="Choose the subject of your course."

    />

    {{-- Course Number Field --}}
    <x-input-text name="course_number"
                  label="Course Number"
                  :value="old('course_number')"
                  classes="col-2"
                  helptext='Enter the course number (e.g., "122" for course BI 122). Enter "0000" if the course has no number.'

    />

    {{-- Course CRN Field --}}
    <x-input-text name="course_crn"
                  label="Course CRN"
                  :value="old('course_crn')"
                  classes="col-2"
                  helptext="Enter the 5-digit CRN for your course. Enter 99999 if the course has no CRN."
    />

    {{-- Number of Students Field --}}
    <x-input-text name="number_of_students"
                  label="Number of Students"
                  type="number"
                  :value="old('number_of_students')"
                  classes="col-3"
                  helptext="Enter the number of students in the class."

    />
</fieldset>

<fieldset class="row mb-4">
    <legend class="col-12 mb-4">ADA information</legend>

    {{-- ADA Provisions Needed Field --}}
    <x-input-checkbox name="ada_provisions_needed"
                      label="ADA Provisions Needed"
                      :checked="old('ada_provisions_needed')"
                      classes="col-4"
    />

    {{-- ADA Provisions Description Field --}}
    <x-textarea name="ada_provisions_description"
                label="ADA Provisions Description"
                :value="old('ada_provisions_description')"
                helptext="Describe the ADA accommodations needed for your class."
                classes="col-8"
    />
</fieldset>

<fieldset class="row mb-4">
    <legend class="col-12 mb-4">Date, time and duration</legend>

    {{-- Preferred Datetime Field --}}
    <x-input-datetime name="preferred_datetime"
                      label="Preferred Datetime"
                      :value="old('preferred_datetime')"
                      helptext="Enter the date and time you would prefer to have your instruction session."
                      classes="col-3"
    />

    {{-- Alternate Datetime Field --}}
    <x-input-datetime name="alternate_datetime"
                      label="Alternate Datetime"
                      :value="old('alternate_datetime')"
                      helptext="Enter an alternate date and time for your instruction session."
                      classes="col-3"

    />

    {{-- Duration Field --}}
    <x-input-select name="duration"
                    label="Duration"
                    :options="['50' => '50', '70' => '70', '110' => '110', '170' => '170']"
                    :selected="old('duration')"
                    helptext="Enter the length of instruction you would like your class to receive, in minutes."
                    classes="col-3"
    />


    {{-- Asynchronous Instruction Ready Date Field --}}
    <x-input-date name="asynchronous_instruction_ready_date"
                  label="Ready Date"
                  :value="old('asynchronous_instruction_ready_date')"
                  classes="col-3"
                  helptext="Asynchronous instruction ready date."
    />
</fieldset>

<fieldset class="row mb-4">
    <legend class="col-12 mb-4">Status & outcomes</legend>

    {{-- Selected Extra Time --}}
{{--    <x-input-checkbox name="need_extra_time"--}}
{{--          label="I need extra time with class"--}}
{{--          :checked="old('need_extra_time')"--}}
{{--          classes="col-3"--}}
{{--    />--}}

    {{-- Extra Time With Class Field --}}
    <x-textarea name="extra_time_with_class"
            label="Extra Time With Class"
            :value="old('extra_time_with_class')"
            helptext="Do you need time to discuss non-library matters with your class on the day of library instruction?"
            classes="col-9"
    />
</fieldset>

<fieldset class="row mb-4">
    <legend class="col-12 mb-4">Assignment Description & Sample Topics</legend>
    <!-- Assessments (File Upload) -->
    <x-input-file
        name="instructor_attachments"
        label="Attach files"
        :multiple="true"
        classes="col-9 mb-4"
        helptext="Upload class assignment(s) and other relevant documents."
    />

</fieldset>


<fieldset class="row mb-4">
    <legend class="col-12 mb-4">What are your learning outcomes for this session?</legend>

    <div class="col-3">
        {{-- Received Assignment Field --}}
        <x-input-checkbox name="received_assignment"
                          label="Received Assignment"
                          :checked="old('received_assignment')"
        />

        {{-- Selected Topics Field --}}
        <x-input-checkbox name="selected_topics"
                          label="Selected Topics"
                          :checked="old('selected_topics')"
        />

        {{-- Explored Background Field --}}
        <x-input-checkbox name="explored_background"
                          label="Explored Background"
                          :checked="old('explored_background')"
        />

        {{-- Written Draft Field --}}
        <x-input-checkbox name="written_draft"
                          label="Written Draft"
                          :checked="old('written_draft')"
        />

        {{-- Other Learning Outcome Field --}}
        <x-input-checkbox name="other_learning_outcome"
                          label="Other Learning Outcome"
                          :checked="old('other_learning_outcome')"
        />
    </div>

    <div class="col-9">
        {{-- Other Learning Outcome Description Field --}}
        <x-textarea name="other_learning_outcome_description"
                    label="Learning Outcomes"
                    :value="old('other_learning_outcome_description')"
        />
    </div>
</fieldset>

<fieldset class="row mb-4">
{{--    <legend class="col-12 mb-4">What do you want your students to get out of library instruction?</legend>--}}
    <x-textarea name="library_instruction_description"
                label="What do you want your students to get out of library instruction?"
                :value="old('other_learning_outcome_description')"
                classes="col-9"
                helptext="Some possible learning outcomes for this session: Develop a search strategy based on their research topic Perform an efficient search of the library catalog for books and other materials Differentiate between scholarly journals and magazines Construct a query for journal, magazine, or newspaper articles and evaluate best choices in the results list Physically locate items and other resources"
    />
</fieldset>

<fieldset class="row mb-4">

        {{-- genai_discussion_interest Field --}}
        <x-textarea name="genai_discussion_interest"
                    label="Gen AI"
                    :value="old('genai_discussion_interest')"
                    classes="col-9"
                    helptext="Need a prompt or description for this field."
        />

</fieldset>
