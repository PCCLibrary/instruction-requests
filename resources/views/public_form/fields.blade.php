<x-fieldset legend="Contact information">
        {{-- Instructor Name --}}

<x-row>
    <x-input-text name="name"
                      label="Instructor name"
                      :value="old('name')"
{{--                      helptext="Instructor name"--}}
                      classes="col-lg-6"
                      required=true
        />

        {{-- Display Name --}}
        <x-input-text name="display_name"
                      label="Students refer to me as"
                      :value="old('display_name')"
{{--                      helptext='""'--}}
                      classes="col-lg-6"
        />

</x-row>
    <x-row>
        {{-- Pronouns --}}
            <x-input-text name="pronouns"
                          label="Pronouns"
                          :value="old('pronouns')"
                          classes="col-lg-4"
    {{--                      helptext='Preferred pronouns'--}}

            />

            {{-- Email --}}
            <x-input-text name="email"
                          label="Email"
                          :value="old('email')"
                          classes="col-lg-4"
                          required=true
            />

            {{-- Phone --}}
            <x-input-text name="phone"
                          label="Phone"
                          :value="old('phone')"
                          classes="col-lg-4"
            />
    </x-row>
</x-fieldset>


<x-fieldset legend="Instruction request information">

    <x-row>
    {{-- Instruction Type Field --}}
    <x-input-select name="instruction_type"
                    label="Instruction Type"
                    :options="['In-Person' => 'In-Person', 'Online' => 'Online', 'Hybrid' => 'Hybrid']"
                    :selected="old('instruction_type')"
                    classes="col-lg-6"
                    helptext=""
    />

    {{-- Course Modality Field --}}
    <x-input-select name="course_modality"
                    label="Instruction Mode"
                    :options="['Synchronous' => 'Synchronous', 'Asynchronous' => 'Asynchronous', 'I\'m not sure' => 'I\'m not sure']"
                    :selected="old('course_modality')"
                    helptext="Examples of asynchronous instruction include the development of tutorials, videos, research guides, having a librarian embedded in your Brightspace classroom to support students, and more."
                    classes="col-lg-6"
    />
    </x-row>

    <x-row>
    {{--     Librarian Preference Field--}}
    <x-input-select name="librarian_id"
                    label="Librarian Preference"
                    :options="$librarians->pluck('display_name', 'id')->toArray()"
                    :selected="old('librarian_id')"
                    classes="col-lg-6"
                    helptext="Do you want to work with a specific librarian or a librarian from a specific campus? We will make every effort to assign your preferred librarian, but we can't guarantee their availability."
    />

    {{--     Campus ID Field--}}
    <x-input-select name="campus_id"
                    label="Class Location"
                    :options="$campuses"
                    :selected="old('campus_id')"
                    classes="col-lg-6"
                    helptext="Choose the campus with which you are primarily associated (if online) ?"
    />

    </x-row>

    <x-row>
    {{--     Department Field--}}
    <x-input-select name="department"
                    label="Subject"
                    :options="$departments"
                    :selected="old('department')"
                    classes="col-lg-4"
                    helptext="Choose the subject of your course."
                    required=true

    />

    {{-- Course Number Field --}}
    <x-input-text name="course_number"
                  label="Course Number"
                  :value="old('course_number')"
                  classes="col-lg-3"
                  helptext='Enter the course number (e.g., "122" for course BI 122). Enter "0000" if the course has no number.'
                  required=true

    />

    {{-- Course CRN Field --}}
    <x-input-text name="course_crn"
                  label="Course CRN"
                  :value="old('course_crn')"
                  classes="col-lg-3"
                  helptext="Enter the 5-digit CRN for your course. Enter 99999 if the course has no CRN."
                  required=true
    />

    {{-- Number of Students Field --}}
    <x-input-text name="number_of_students"
                  label="Number of Students"
                  type="number"
                  :value="old('number_of_students')"
                  classes="col-lg-2"
                  helptext="Enter the number of students in the class."

    />
    </x-row>

    <x-row>
        <!-- Assessments (File Upload) -->
        <x-input-file
            name="class_syllabus"
            label="Attach syllabus (doc, pdf, or txt)"
            :multiple="true"
            classes="col-lg-6"
            helptext="Please attach the class syllabus."
        />


    </x-row>
</x-fieldset>

<x-fieldset legend="ADA information">

    <x-row>
    {{-- ADA Provisions Needed Field --}}
    <x-input-checkbox name="ada_provisions_needed"
                      label="ADA Provisions Needed"
                      :checked="old('ada_provisions_needed')"
                      classes="col-lg-4"
                      target="ada_provisions_description"
    />

    {{-- ADA Provisions Description Field --}}
    <x-textarea name="ada_provisions_description"
                label="Describe the ADA accommodations needed for your class."
                :value="old('ada_provisions_description')"
{{--                helptext="Describe the ADA accommodations needed for your class."--}}
                classes="col-lg-8 invisible"
    />
    </x-row>
</x-fieldset>

<x-fieldset legend="Date, time and duration">

    <x-row>
    {{-- Preferred Datetime Field --}}
    <x-input-datetime name="preferred_datetime"
                      label="Preferred Date & Time"
                      :value="old('preferred_datetime')"
                      helptext="Enter the date and time you would prefer to have your instruction session."
                      classes="col-lg-3"
                      required=true
    />

    {{-- Alternate Datetime Field --}}
    <x-input-datetime name="alternate_datetime"
                      label="Alternate Date & Time"
                      :value="old('alternate_datetime')"
                      helptext="Enter an alternate date and time for your instruction session."
                      classes="col-lg-3"
                      required=true
    />

    {{-- Duration Field --}}
    <x-input-text name="duration"
                    label="Duration"
                    :selected="old('duration')"
                    helptext="Enter the length of instruction you would like your class to receive, in minutes."
                    classes="col-lg-3"
                  required=true
    />


    {{-- Asynchronous Instruction Ready Date Field --}}
    <x-input-date name="asynchronous_instruction_ready_date"
                  label="Asynchronous instruction ready by"
                  :value="old('asynchronous_instruction_ready_date')"
                  classes="col-lg-3"
{{--                  helptext="Asynchronous instruction ready date."--}}
    />
    </x-row>

    <x-row>
    {{-- Extra Time With Class Field --}}
    <x-textarea name="extra_time_with_class"
            label="Do you need time to discuss non-library matters with your class on the day of library instruction?"
            :value="old('extra_time_with_class')"
{{--            helptext="Do you need time to discuss non-library matters with your class on the day of library instruction?"--}}
            classes="col-lg-9"
    />
    </x-row>
</x-fieldset>

<x-fieldset legend="Assignments">
    <x-row>
    <!-- Assessments (File Upload) -->
    <x-input-file
        name="instructor_attachments"
        label="Attach documents (doc, pdf, or txt)"
        :multiple="true"
        classes="col-lg-6"
        helptext="Upload class assignment(s) and other relevant documents."
    />
        <x-textarea name="assignment_description"
                    label="Assignment description and sample topics"
                    :value="old('assignment_description')"
                    classes="col-lg-6"
        />

    </x-row>

</x-fieldset>

<x-fieldset legend="By the time students receive library instruction they will have:">

    <x-row>
    <div class="col-lg-3">
        {{-- Received Assignment Field --}}
        <x-input-checkbox name="received_assignment"
                          label="Received Assignment"
                          :value="old('assignment_description')"
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
                          target="other_learning_outcome_description"
        />
    </div>

    <div class="col-lg-9">
        {{-- Other Learning Outcome Description Field --}}
        <x-textarea name="other_learning_outcome_description"
                    label="Other Learning Outcome"
                    :value="old('other_learning_outcome_description')"
                    classes="invisible"
        />
    </div>
    </x-row>
</x-fieldset>

<x-fieldset>
    <x-row>
    <x-textarea name="library_instruction_description"
                label="What do you want your students to get out of library instruction?"
                :value="old('other_learning_outcome_description')"
                classes="col-lg-9"
                helptext="Some possible learning outcomes for this session: Develop a search strategy based on their research topic Perform an efficient search of the library catalog for books and other materials Differentiate between scholarly journals and magazines Construct a query for journal, magazine, or newspaper articles and evaluate best choices in the results list Physically locate items and other resources"
    />
    </x-row>
</x-fieldset>

<x-fieldset>
    <x-row>

        {{-- genai_discussion_interest Field --}}
        <x-textarea name="genai_discussion_interest"
                    label="Generative AI"
                    :value="old('genai_discussion_interest')"
                    classes="col-lg-9"
                    helptext="We sometimes use Generative AI tools such as ChatGPT and Perplexity with students in the research process. If you have class guidelines or a syllabus statement around the use of GenAI, or would like to connect with the librarian teaching your class about this, please share that here."
        />

    </x-row>
</x-fieldset>
