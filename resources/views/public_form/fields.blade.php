@php
    // Example arrays from controller (unchanged)
    // $campuses = [1 => 'Main Campus', 2 => 'North Campus', 99 => 'Other'];
    // $librarians = collect([['id' => 2, 'display_name' => 'John Smith']]);
    // $departments = ['BI' => 'Biology', 'EN' => 'English', 'CS' => 'Computer Science'];
@endphp

@extends('layouts.app')

@section('content')
    <div
        x-data="instructionForm()"
        x-init="init()"
        class="card"
    >
        <form
            method="POST"
            action="{{ route('public.instruction-request.store') }}"
            id="instructionRequestForm"
            enctype="multipart/form-data"
        >
            @csrf

            {{-- ************************
                 Contact Information
            ************************ --}}
            <x-fieldset legend="Contact information" classes="card-body">

                <x-row>
                    <x-input-text
                        name="name"
                        label="Instructor name"
                        :value="old('name')"
                        classes="col-lg-6"
                        required=true
                    />

                    <x-input-text
                        name="display_name"
                        label="Students refer to me as"
                        :value="old('display_name')"
                        classes="col-lg-6"
                    />
                </x-row>

                <x-row>
                    <x-input-text
                        name="pronouns"
                        label="Pronouns"
                        :value="old('pronouns')"
                        classes="col-lg-4"
                    />

                    <x-input-text
                        name="email"
                        label="Email"
                        :value="old('email')"
                        classes="col-lg-4"
                        required=true
                    />

                    <x-input-text
                        name="phone"
                        label="Phone"
                        :value="old('phone')"
                        classes="col-lg-4"
                    />
                </x-row>
            </x-fieldset>


            {{-- ************************
                 Request Information
            ************************ --}}
            <x-fieldset legend="Request information" classes="card-body">
                <x-row>
                    <x-input-select
                        name="instruction_type"
                        label="Instruction Type"
                        :options="[
                        'on-campus' => 'Librarian joins my class on campus',
                        'remote' => 'Librarian joins my remote class',
                        'asynchronous' => 'Librarian provides resources to be used asynchronously'
                    ]"
                        :selected="old('instruction_type')"
                        classes="col-lg-6"
                        tophelptext="Please select what you need help with."
                        required=true
                        {{--
                          Whenever this select changes, Alpine will call applyFieldSettings()
                          to show/hide the correct fieldsets and set required/disabled fields.
                        --}}
                        x-model="instructionType"
                        @change="applyFieldSettings"
                    />
                </x-row>
            </x-fieldset>


            {{-- ************************
                 Campus + Librarian
            ************************ --}}
            <x-fieldset classes="on-campus remote asynchronous card-body">
                <x-row>
                    {{-- Keep hidden campus_id; you can remove if unneeded, but we’ll preserve it exactly. --}}
                    <input type="hidden" name="campus_id" value="1" />

                    <x-input-select
                        name="campus_id"
                        label="Class Location"
                        :options="$campuses"
                        :selected="old('campus_id')"
                        classes="col-lg-6"
                        tophelptext="Select the location where your class takes place or is assigned to.
                        If the class is not assigned to a location, select the campus with which you are primarily
                        associated. Select “other” if you’re not sure."
                        required=true
                    />
                </x-row>
            </x-fieldset>

            <x-fieldset classes="on-campus remote card-body">
                <x-row>
                    <input name="librarian_id" type="hidden" value="2" />
                    <x-input-select
                        name="librarian_id"
                        label="Librarian Preference"
                        :options="$librarians->pluck('display_name', 'id')->toArray()"
                        :selected="old('librarian_id')"
                        classes="col-lg-6"
                        tophelptext="Do you want to work with a librarian from a specific campus or a specific librarian?
                        We will try to assign your preferred librarian, but we can’t guarantee their availability."
                        required=false
                    />
                </x-row>
            </x-fieldset>


            {{-- ************************
                 Class Information
            ************************ --}}
            <x-fieldset legend="Class Information" classes="on-campus remote asynchronous card-body">
                <x-row>
                    <x-input-select
                        name="department"
                        label="Subject"
                        :options="$departments"
                        :selected="old('department')"
                        classes="col-lg-4"
                        helptext="Choose the subject of your course."
                        required=true
                    />

                    <x-input-text
                        name="course_number"
                        label="Course Number"
                        :value="old('course_number')"
                        classes="col-lg-3"
                        helptext='Enter the course number (e.g., "122" for course BI 122). Enter "0000" if no course number.'
                        required=true
                    />

                    <x-input-text
                        name="course_crn"
                        label="Course CRN"
                        :value="old('course_crn')"
                        classes="col-lg-3"
                        helptext="Enter the 5-digit CRN for your course. Enter 99999 if no CRN."
                        required=true
                    />

                    <x-input-text
                        name="number_of_students"
                        label="Number of Students"
                        type="number"
                        :value="old('number_of_students')"
                        classes="col-lg-2 on-campus remote"
                        helptext="Enter the number of students in the class."
                        required=true
                    />
                </x-row>
            </x-fieldset>


            {{-- ************************
                 ADA Fields
            ************************ --}}
            <x-fieldset classes="on-campus remote card-body">
                <x-row>
                    <x-input-checkbox
                        name="ada_provisions_needed"
                        label="ADA Provisions Needed"
                        :checked="old('ada_provisions_needed')"
                        classes="col-lg-3"
                        target="ada_provisions_description"
                    />

                    <x-input-textarea
                        name="ada_provisions_description"
                        label="Describe the ADA accommodations needed for your class."
                        :value="old('ada_provisions_description')"
                        classes="col-lg-9 {{ empty(old('ada_provisions_description')) ? 'invisible' : '' }}"
                    />
                </x-row>
            </x-fieldset>


            {{-- ************************
                 Attachments
            ************************ --}}
            <x-fieldset legend="Attachments" classes="on-campus remote asynchronous card-body">
                <x-row>
                    <x-input-file
                        name="instructor_attachments"
                        label="Attach assignment (doc, pdf, or txt)"
                        :multiple="true"
                        :errors="$errors->get('instructor_attachments.*')"
                        classes="col-lg-6"
                    />

                    <x-input-file
                        name="class_syllabus"
                        label="Attach syllabus (doc, pdf, or txt)"
                        :multiple="true"
                        :errors="$errors->get('class_syllabus.*')"
                        classes="col-lg-6"
                    />
                </x-row>

                <x-row>
                    <x-input-textarea
                        name="assignment_description"
                        label="Assignment description and sample topics"
                        :value="old('assignment_description')"
                        classes="col-lg-6"
                    />
                </x-row>

                <x-row>
                    <x-input-textarea
                        name="class_description"
                        label="Additional Notes (optional)"
                        :value="old('class_description')"
                        classes="col-lg-8"
                        helptext="If you have additional information for your class, or Google Drive links for materials,
                        please provide them here."
                    />
                </x-row>
            </x-fieldset>


            {{-- ************************
                 Date/Time Fields
            ************************ --}}
            <x-fieldset legend="Date, time and duration" classes="on-campus remote card-body">
                <x-row>
                    <input type="hidden" name="preferred_datetime" value="{{ now()->format('Y-m-d H:i:s') }}" />

                    <x-input-datetime
                        name="preferred_datetime"
                        label="Preferred Date & Time"
                        :value="old('preferred_datetime')"
                        helptext="Enter the date/time you prefer to have your instruction session."
                        classes="col-lg-4"
                    />

                    <input type="hidden" name="alternate_datetime" value="{{ now()->format('Y-m-d H:i:s') }}" />

                    <x-input-datetime
                        name="alternate_datetime"
                        label="Alternate Date & Time"
                        :value="old('alternate_datetime')"
                        helptext="Enter an alternate date/time for your instruction session."
                        classes="col-lg-4"
                    />

                    <input type="hidden" name="duration" value="0" />
                    <x-input-text
                        name="duration"
                        label="Duration"
                        :selected="old('duration')"
                        helptext="Please enter the duration of your class in minutes only."
                        classes="col-lg-4"
                    />
                </x-row>
            </x-fieldset>

            <x-fieldset legend="Extra Time" classes="on-campus card-body">
                <x-row>
                    <x-input-textarea
                        name="extra_time_with_class"
                        label="Do you need time to discuss non-library matters with your class on the day of library instruction?"
                        :value="old('extra_time_with_class')"
                        classes="col-lg-8"
                    />
                </x-row>
            </x-fieldset>


            {{-- ************************
                 Asynchronous Date
            ************************ --}}
            <x-fieldset legend="Asynchronous Date" classes="asynchronous card-body">
                <x-row>
                    <x-input-date
                        name="asynchronous_instruction_ready_date"
                        label="Asynchronous instruction ready by"
                        :value="old('asynchronous_instruction_ready_date')"
                        helptext="Examples of asynchronous instruction: tutorials, videos, research guides, or a librarian
                        embedded in Brightspace."
                        classes="col-lg-6"
                    />
                </x-row>
            </x-fieldset>


            {{-- ************************
                 Learning Outcomes
            ************************ --}}
            <x-fieldset legend="By the time students receive library instruction they will have:" classes="on-campus remote card-body">
                <x-row>
                    <div class="col-lg-3">
                        <x-input-checkbox
                            name="received_assignment"
                            label="Received Assignment"
                            :checked="old('received_assignment')"
                        />
                        <x-input-checkbox
                            name="selected_topics"
                            label="Selected Topics"
                            :checked="old('selected_topics')"
                        />
                        <x-input-checkbox
                            name="explored_background"
                            label="Explored Background"
                            :checked="old('explored_background')"
                        />
                    </div>
                    <div class="col-lg-3">
                        <x-input-checkbox
                            name="written_draft"
                            label="Written Draft"
                            :checked="old('written_draft')"
                        />
                        <x-input-checkbox
                            name="other_learning_outcome"
                            label="Other Learning Outcome"
                            :checked="old('other_learning_outcome')"
                            target="other_learning_outcome_description"
                        />
                    </div>
                    <div class="col-lg-6">
                        <x-input-textarea
                            name="other_learning_outcome_description"
                            label="Other Learning Outcome"
                            :value="old('other_learning_outcome_description')"
                            classes="invisible"
                        />
                    </div>
                </x-row>
            </x-fieldset>

            <x-fieldset classes="on-campus remote asynchronous card-body">
                <x-row>
                    <x-input-textarea
                        name="library_instruction_description"
                        label="What do you want your students to get out of library instruction?"
                        :value="old('library_instruction_description')"
                        classes="col-lg-8"
                        helptext="Examples: developing a topic, searching effectively, evaluating sources, etc."
                    />
                </x-row>
            </x-fieldset>

            <x-fieldset classes="on-campus remote asynchronous card-body">
                <x-row>
                    <x-input-textarea
                        name="genai_discussion_interest"
                        label="Generative AI"
                        :value="old('genai_discussion_interest')"
                        classes="col-lg-8"
                        helptext="If you have class guidelines about ChatGPT, Perplexity, etc., or want to coordinate
                        with your librarian on AI usage, share details here."
                    />
                </x-row>
            </x-fieldset>

            {{-- ************************
                 Form Footer
            ************************ --}}
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
                <button class="btn btn-warning ml-2" id="clearForm" type="button">Clear Form</button>
            </div>
        </form>
    </div>

    {{-- Alpine.js Logic to Mimic Original jQuery Field Settings --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('instructionForm', () => ({
                instructionType: '{{ old('instruction_type') ?? '' }}',

                // This mirrors your original jQuery-based fieldSettings object
                fieldSettings: {
                    'on-campus': {
                        show: '.on-campus',
                        required: [
                            '#number_of_students',
                            '#campus_id',
                            '#preferred_datetime',
                            '#duration'
                        ],
                        notRequired: [
                            '#librarian_id',
                            '#asynchronous_instruction_ready_date',
                            '#alternate_datetime'
                        ],
                        disable: ['#asynchronous_instruction_ready_date']
                    },
                    'remote': {
                        show: '.remote',
                        required: [
                            '#librarian_id',
                            '#preferred_datetime',
                            '#duration',
                            '#campus_id'
                        ],
                        notRequired: [
                            '#number_of_students',
                            '#asynchronous_instruction_ready_date',
                            '#alternate_datetime'
                        ],
                        disable: ['#asynchronous_instruction_ready_date']
                    },
                    'asynchronous': {
                        show: '.asynchronous',
                        required: [
                            '#asynchronous_instruction_ready_date',
                            '#campus_id'
                        ],
                        notRequired: [
                            '#librarian_id',
                            '#number_of_students',
                            '#preferred_datetime',
                            '#alternate_datetime',
                            '#duration'
                        ],
                        disable: [
                            '#librarian_id',
                            '#preferred_datetime',
                            '#alternate_datetime',
                            '#duration'
                        ]
                    }
                },

                init() {
                    // On load, hide/show relevant sections
                    this.applyFieldSettings();

                    // If you *still* want select2, you could init it here.
                    // Example:
                    // $('#librarian_id, #department, #campus_id').select2({
                    //     theme: 'bootstrap4',
                    //     width: '100%'
                    // });
                },

                applyFieldSettings() {
                    // 1) Hide all
                    ['on-campus','remote','asynchronous'].forEach(cls => {
                        document.querySelectorAll('.' + cls).forEach(el => {
                            el.classList.add('d-none');
                        });
                    });

                    // 2) Show only the relevant fieldset(s)
                    const currentSetting = this.fieldSettings[this.instructionType];
                    if (currentSetting) {
                        const selector = currentSetting.show;
                        document.querySelectorAll(selector).forEach(el => {
                            el.classList.remove('d-none');
                        });

                        // 3) Enable all fields by default, then adjust
                        document.querySelectorAll('input, select, textarea').forEach(el => {
                            el.disabled = false;
                            el.removeAttribute('required');
                            el.closest('label')?.classList?.remove('is-required');
                        });

                        // 4) Mark fields as required
                        currentSetting.required.forEach(sel => {
                            let field = document.querySelector(sel);
                            if (field) {
                                field.setAttribute('required', true);
                                let label = document.querySelector(`label[for="${field.id}"]`);
                                label?.classList?.add('is-required');
                            }
                        });

                        // 5) Mark fields as NOT required
                        currentSetting.notRequired.forEach(sel => {
                            let field = document.querySelector(sel);
                            if (field) {
                                field.removeAttribute('required');
                                let label = document.querySelector(`label[for="${field.id}"]`);
                                label?.classList?.remove('is-required');
                            }
                        });

                        // 6) Disable certain fields
                        currentSetting.disable.forEach(sel => {
                            let field = document.querySelector(sel);
                            if (field) {
                                field.disabled = true;
                            }
                        });
                    }
                    else {
                        // No instruction type selected, so keep everything hidden
                    }
                }
            }));
        });
    </script>
@endsection
