{!! Form::open(['route' => 'public.instruction-request.store', 'id' => 'instructionRequestForm', 'files' => true]) !!}

<x-fieldset legend="Contact information" classes="card-body">

    <x-row>
        {{-- name --}}
        <x-input-text name="name"
              label="Instructor name"
              :value="old('name')"
              classes="col-lg-6"
              required=true
            />

            {{-- Display Name --}}
            <x-input-text name="display_name"
              label="Students refer to me as"
              :value="old('display_name')"
              classes="col-lg-6"
            />

    </x-row>
    <x-row>
            {{-- pronouns --}}
            <x-input-text name="pronouns"
              label="Pronouns"
              :value="old('pronouns')"
              classes="col-lg-4"
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

<x-fieldset legend="Request information" classes="card-body">

    <x-row>
        {{-- Instruction Type Field --}}
        <x-input-select name="instruction_type"
            label="Instruction Type"
            :options="[
            'on-campus' => 'Librarian joins my class on campus',
            'remote' => 'Librarian joins my remote class',
            'asynchronous' => 'Librarian provides resources to be used asynchronously']"
            :selected="old('instruction_type')"
            classes="col-lg-6"
            tophelptext="Please select what you need help with."
            required=true
        />

    </x-row>
</x-fieldset>

<x-fieldset classes="on-campus remote asynchronous card-body">

    <x-row>
        <input type="hidden" name="campus_id" value="1" />
        {{--     Campus ID Field--}}
        <x-input-select name="campus_id"
                        label="Class Location"
                        :options="$campuses"
                        :selected="old('campus_id')"
                        classes="col-lg-6"
                        tophelptext="Choose the campus with which you are primarily associated (if online) ?"
                        required=true
        />

    </x-row>
</x-fieldset>

<x-fieldset classes="on-campus remote card-body">

    <x-row>

        {{--     default Librarian Preference --}}
        <input name="librarian_id" type="hidden" value="2" />

        {{--     Librarian Preference Field--}}
        <x-input-select name="librarian_id"
            label="Librarian Preference"
            :options="$librarians->pluck('display_name', 'id')->toArray()"
            :selected="old('librarian_id')"
            classes="col-lg-6"
            tophelptext="Do you want to work with a specific librarian or a librarian from a specific campus? We will make every effort to assign your preferred librarian, but we can't guarantee their availability."
            required=false
        />

    </x-row>

</x-fieldset>


<x-fieldset legend="Class Information" classes="on-campus remote asynchronous card-body">

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
            classes="col-lg-2 on-campus remote"
            helptext="Enter the number of students in the class."
            required=true
        />
    </x-row>

    <x-row>

        {{-- class_description --}}
        <x-textarea name="class_description"
            label="Additional notes about your class. If you have google drive links for your materials, please provide them here."
            :value="old('class_description')"
            classes="col-lg-8"
        />

    </x-row>

</x-fieldset>


<x-fieldset classes="on-campus remote card-body">

    <x-row>
        {{-- ADA Provisions Needed Field --}}
        <x-input-checkbox name="ada_provisions_needed"
                          label="ADA Provisions Needed"
                          :checked="old('ada_provisions_needed')"
                          classes="col-lg-3"
                          target="ada_provisions_description"
        />

        {{-- ADA Provisions Description Field --}}
        <x-textarea name="ada_provisions_description"
                    label="Describe the ADA accommodations needed for your class."
                    :value="old('ada_provisions_description')"
                    {{--                helptext="Describe the ADA accommodations needed for your class."--}}
                    classes="col-lg-9 {{ empty(old('ada_provisions_description')) ? 'invisible' : '' }}"
        />
    </x-row>

</x-fieldset>

<x-fieldset legend="Attachments" classes="on-campus remote asynchronous card-body">

    <x-row>
        <!-- Assessments (File Upload) -->
        <x-input-file
            name="instructor_attachments"
            label="Attach assignment (doc, pdf, or txt)"
            :multiple="true"
            :errors="$errors->get('instructor_attachments.*')"
            classes="col-lg-6"
{{--            helptext="Upload class assignment(s) and other relevant documents. "--}}
        />

        {{-- class_syllabus (File Upload) --}}
        <x-input-file
            name="class_syllabus"
            label="Attach syllabus (doc, pdf, or txt)"
            :multiple="true"
            :errors="$errors->get('class_syllabus.*')"
            classes="col-lg-6"
            {{--            helptext="Please attach the class class syllabus."--}}
        />

    </x-row>

    <x-row>
        <x-textarea name="assignment_description"
                    label="Assignment description and sample topics"
                    :value="old('assignment_description')"
                    classes="col-lg-6"
        />
    </x-row>

</x-fieldset>

<x-fieldset legend="Date, time and duration" classes="on-campus remote card-body">

    <x-row>
        {{-- default preferred_datetime --}}
        <input type="hidden" name="preferred_datetime" value="{{ now()->format('Y-m-d H:i:s') }}" />

        {{-- Preferred Datetime Field --}}
        <x-input-datetime name="preferred_datetime"
          label="Preferred Date & Time"
          :value="old('preferred_datetime')"
          helptext="Enter the date and time you would prefer to have your instruction session."
          classes="col-lg-4"
          required=true
        />

        {{-- default alternate_datetime --}}
        <input type="hidden" name="alternate_datetime" value="{{ now()->format('Y-m-d H:i:s') }}" />

        {{-- Alternate Datetime Field --}}
        <x-input-datetime name="alternate_datetime"
            label="Alternate Date & Time"
            :value="old('alternate_datetime')"
            helptext="Enter an alternate date and time for your instruction session."
            classes="col-lg-4"
            required=false
        />

        <input type="hidden" name="duration" value="0" />
        {{-- Duration Field --}}
        <x-input-duration name="duration"
            label="Duration"
            :selected="old('duration')"
            helptext="Enter the length of instruction you would like your class to receive, in hours and minutes."
            classes="col-lg-4"
            required=true
        />

    </x-row>
</x-fieldset>

<x-fieldset legend="Extra Time" classes="on-campus card-body">
    <x-row>
    {{-- Extra Time With Class Field --}}
        <x-textarea name="extra_time_with_class"
            label="Do you need time to discuss non-library matters with your class on the day of library instruction?"
            :value="old('extra_time_with_class')"
            classes="col-lg-8"
        />
    </x-row>
</x-fieldset>

<x-fieldset legend="Asynchronous Date" classes="asynchronous card-body">
    <x-row>
        {{-- Asynchronous Instruction Ready Date Field --}}
        <x-input-date name="asynchronous_instruction_ready_date"
                      label="Asynchronous instruction ready by"
                      :value="old('asynchronous_instruction_ready_date')"
                      helptext="Examples of asynchronous instruction include the development of tutorials, videos, research guides, having a librarian embedded in your Brightspace classroom to support students, and more."
                      classes="col-lg-6"
        />
    </x-row>
</x-fieldset>

<x-fieldset legend="By the time students receive library instruction they will have:" classes="on-campus remote card-body">

    <x-row>
        <div class="col-lg-3">
            {{-- Received Assignment Field --}}
                <x-input-checkbox name="received_assignment"
                  label="Received Assignment"
                  :value="old('received_assignment')"
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

        </div>
        <div class="col-lg-3">

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

                <div class="col-lg-6">
                {{-- Other Learning Outcome Description Field --}}
                <x-textarea name="other_learning_outcome_description"
                    label="Other Learning Outcome"
                    :value="old('other_learning_outcome_description')"
                    classes="invisible"
                />
        </div>
    </x-row>
</x-fieldset>

<x-fieldset classes="on-campus remote asynchronous card-body">
    <x-row>
        <x-textarea name="library_instruction_description"
                    label="What do you want your students to get out of library instruction?"
                    :value="old('library_instruction_description')"
                    classes="col-lg-8"
                    helptext="Some possible learning outcomes for this session: Develop a search strategy based on their research topic Perform an efficient search of the library catalog for books and other materials Differentiate between scholarly journals and magazines Construct a query for journal, magazine, or newspaper articles and evaluate best choices in the results list Physically locate items and other resources"
        />
    </x-row>
</x-fieldset>

<x-fieldset classes="on-campus remote asynchronous card-body">
    <x-row>

        {{-- genai_discussion_interest Field --}}
        <x-textarea name="genai_discussion_interest"
                    label="Generative AI"
                    :value="old('genai_discussion_interest')"
                    classes="col-lg-8"
                    helptext="We sometimes use Generative AI tools such as ChatGPT and Perplexity with students in the research process. If you have class guidelines or a syllabus statement around the use of GenAI, or would like to connect with the librarian teaching your class about this, please share that here."
        />

    </x-row>
</x-fieldset>

<div class="card-footer">
    {!! Form::submit('Submit', ['class' => 'btn btn-primary']) !!}
    <button class="btn btn-warning ml-2" id="clearForm" type="button">Clear Form</button>
</div>


{!! Form::close() !!}
