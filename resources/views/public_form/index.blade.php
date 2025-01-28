@extends('layouts.public')

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
            <fieldset class="card-body">
                <legend>Contact information</legend>
                <div class="row">
                    @include('public_form.partials.input-text', [
                        'name' => 'name',
                        'label' => 'Instructor name',
                        'value' => old('name'),
                        'classes' => 'col-lg-6',
                        'required' => true
                    ])
                    @include('public_form.partials.input-text', [
                        'name' => 'display_name',
                        'label' => 'Students refer to me as',
                        'value' => old('display_name'),
                        'classes' => 'col-lg-6'
                    ])
                </div>
                <div class="row">
                    @include('public_form.partials.input-text', [
                        'name' => 'pronouns',
                        'label' => 'Pronouns',
                        'value' => old('pronouns'),
                        'classes' => 'col-lg-4'
                    ])
                    @include('public_form.partials.input-text', [
                        'name' => 'email',
                        'label' => 'Email',
                        'value' => old('email'),
                        'classes' => 'col-lg-4',
                        'required' => true
                    ])
                    @include('public_form.partials.input-text', [
                        'name' => 'phone',
                        'label' => 'Phone',
                        'value' => old('phone'),
                        'classes' => 'col-lg-4'
                    ])
                </div>
            </fieldset>

            {{-- ************************
                 Request Information
            ************************ --}}
            <fieldset class="card-body">
                <legend>Request information</legend>
                <div class="row">
                    @include('public_form.partials.input-select', [
                        'name' => 'instruction_type',
                        'label' => 'Instruction Type',
                        'options' => [
                            'on-campus' => 'Librarian joins my class on campus',
                            'remote' => 'Librarian joins my remote class',
                            'asynchronous' => 'Librarian provides resources to be used asynchronously'
                        ],
                        'selected' => old('instruction_type'),
                        'classes' => 'col-lg-6',
                        'tophelptext' => 'Please select what you need help with.',
                        'required' => true,
                        'xModel' => 'instructionType',
                        '@change' => 'applyFieldSettings'
                    ])
                </div>
            </fieldset>

            {{-- ************************
                 Campus + Librarian
            ************************ --}}
            <fieldset class="on-campus remote asynchronous card-body">
                <div class="row">
                    <input type="hidden" name="campus_id" value="1" />
                    @include('public_form.partials.input-select', [
                        'name' => 'campus_id',
                        'label' => 'Class Location',
                        'options' => $campuses,
                        'selected' => old('campus_id'),
                        'classes' => 'col-lg-6',
                        'tophelptext' => 'Select the location where your class takes place or is assigned to.
                        If the class is not assigned to a location, select the campus with which you are primarily associated.
                        Select “other” if you’re not sure.',
                        'required' => true
                    ])
                </div>
            </fieldset>

            <fieldset class="on-campus remote card-body">
                <div class="row">
                    <input name="librarian_id" type="hidden" value="2" />
                    @include('public_form.partials.input-select', [
                        'name' => 'librarian_id',
                        'label' => 'Librarian Preference',
                        'options' => $librarians->pluck('display_name', 'id')->toArray(),
                        'selected' => old('librarian_id'),
                        'classes' => 'col-lg-6',
                        'tophelptext' => 'Do you want to work with a librarian from a specific campus or a specific librarian?
                        We will try to assign your preferred librarian, but we can’t guarantee their availability.',
                        'required' => false
                    ])
                </div>
            </fieldset>

            {{-- ************************
                 Class Information
            ************************ --}}
            <fieldset class="on-campus remote asynchronous card-body">
                <legend>Class Information</legend>
                <div class="row">
                    @include('public_form.partials.input-select', [
                        'name' => 'department',
                        'label' => 'Subject',
                        'options' => $departments,
                        'selected' => old('department'),
                        'classes' => 'col-lg-4',
                        'helptext' => 'Choose the subject of your course.',
                        'required' => true
                    ])
                    @include('public_form.partials.input-text', [
                        'name' => 'course_number',
                        'label' => 'Course Number',
                        'value' => old('course_number'),
                        'classes' => 'col-lg-3',
                        'helptext' => 'Enter the course number (e.g., "122" for course BI 122). Enter "0000" if no course number.',
                        'required' => true
                    ])
                    @include('public_form.partials.input-text', [
                        'name' => 'course_crn',
                        'label' => 'Course CRN',
                        'value' => old('course_crn'),
                        'classes' => 'col-lg-3',
                        'helptext' => 'Enter the 5-digit CRN for your course. Enter 99999 if no CRN.',
                        'required' => true
                    ])
                    @include('public_form.partials.input-text', [
                        'name' => 'number_of_students',
                        'label' => 'Number of Students',
                        'type' => 'number',
                        'value' => old('number_of_students'),
                        'classes' => 'col-lg-2 on-campus remote',
                        'helptext' => 'Enter the number of students in the class.',
                        'required' => true
                    ])
                </div>
            </fieldset>

            {{-- ************************
                 ADA Fields
            ************************ --}}
            <fieldset class="on-campus remote card-body">
                <div class="row">
                    @include('public_form.partials.input-checkbox', [
                        'name' => 'ada_provisions_needed',
                        'label' => 'ADA Provisions Needed',
                        'checked' => old('ada_provisions_needed'),
                        'classes' => 'col-lg-3',
                        'target' => 'ada_provisions_description',
                    ])
                    @include('public_form.partials.textarea', [
                        'name' => 'ada_provisions_description',
                        'label' => 'Describe the ADA accommodations needed for your class.',
                        'value' => old('ada_provisions_description'),
                        'classes' => 'col-lg-9 ' . (empty(old('ada_provisions_description')) ? 'invisible' : '')
                    ])
                </div>
            </fieldset>

            {{-- ************************
                 Attachments
            ************************ --}}
            <fieldset class="on-campus remote asynchronous card-body">
                <legend>Attachments</legend>
                <div class="row mb-4">
                    @include('public_form.partials.input-file', [
                        'name' => 'instructor_attachments',
                        'label' => 'Attach assignment (doc, pdf, or txt)',
                        'multiple' => true,
                        'errors' => $errors->get('instructor_attachments.*'),
                        'classes' => 'col-lg-6'
                    ])
                    @include('public_form.partials.input-file', [
                        'name' => 'class_syllabus',
                        'label' => 'Attach syllabus (doc, pdf, or txt)',
                        'multiple' => true,
                        'errors' => $errors->get('class_syllabus.*'),
                        'classes' => 'col-lg-6'
                    ])
                </div>
                <div class="row">
                    @include('public_form.partials.textarea', [
                        'name' => 'assignment_description',
                        'label' => 'Assignment description and sample topics',
                        'value' => old('assignment_description'),
                        'classes' => 'col-lg-6'
                    ])
                </div>
                <div class="row">
                    @include('public_form.partials.textarea', [
                        'name' => 'class_description',
                        'label' => 'Additional Notes (optional)',
                        'value' => old('class_description'),
                        'classes' => 'col-lg-8',
                        'helptext' => 'If you have additional information for your class, or Google Drive links for materials, please provide them here.'
                    ])
                </div>
            </fieldset>

            {{-- ************************
                 Date/Time Fields
            ************************ --}}
            <fieldset class="on-campus remote card-body">
                <legend>Date, time and duration</legend>
                <div class="row">
                    <input type="hidden" name="preferred_datetime" value="{{ now()->format('Y-m-d H:i:s') }}" />
                    @include('public_form.partials.input-datetime', [
                        'name' => 'preferred_datetime',
                        'label' => 'Preferred Date & Time',
                        'value' => old('preferred_datetime'),
                        'helptext' => 'Enter the date/time you prefer to have your instruction session.',
                        'classes' => 'col-lg-4'
                    ])
                    <input type="hidden" name="alternate_datetime" value="{{ now()->format('Y-m-d H:i:s') }}" />
                    @include('public_form.partials.input-datetime', [
                        'name' => 'alternate_datetime',
                        'label' => 'Alternate Date & Time',
                        'value' => old('alternate_datetime'),
                        'helptext' => 'Enter an alternate date/time for your instruction session.',
                        'classes' => 'col-lg-4'
                    ])
                    <input type="hidden" name="duration" value="0" />
                    @include('public_form.partials.input-text', [
                        'name' => 'duration',
                        'label' => 'Duration',
                        'selected' => old('duration'),
                        'helptext' => 'Please enter the duration of your class in minutes only.',
                        'classes' => 'col-lg-4'
                    ])
                </div>
            </fieldset>

            {{-- ************************
                 Extra Time
            ************************ --}}
            <fieldset class="on-campus card-body">
                <div class="row">
                    @include('public_form.partials.textarea', [
                        'name' => 'extra_time_with_class',
                        'label' => 'Do you need time to discuss non-library matters with your class on the day of library instruction?',
                        'value' => old('extra_time_with_class'),
                        'classes' => 'col-lg-8'
                    ])
                </div>
            </fieldset>

            {{-- ************************
                 Asynchronous Date
            ************************ --}}
            <fieldset class="asynchronous card-body">
                <legend>Asynchronous Date</legend>
                <div class="row">
                    @include('public_form.partials.input-date', [
                        'name' => 'asynchronous_instruction_ready_date',
                        'label' => 'Asynchronous instruction ready by',
                        'value' => old('asynchronous_instruction_ready_date'),
                        'helptext' => 'Examples of asynchronous instruction: tutorials, videos, research guides, or a librarian embedded in Brightspace.',
                        'classes' => 'col-lg-6'
                    ])
                </div>
            </fieldset>

            {{-- ************************
                 Learning Outcomes
            ************************ --}}
            <fieldset class="on-campus remote card-body">
                <legend>By the time students receive library instruction they will have:</legend>
                <div class="row">
                    <div class="col-lg-3">
                        @include('public_form.partials.input-checkbox', [
                            'name' => 'received_assignment',
                            'label' => 'Received Assignment',
                            'checked' => old('received_assignment')
                        ])
                        @include('public_form.partials.input-checkbox', [
                            'name' => 'selected_topics',
                            'label' => 'Selected Topics',
                            'checked' => old('selected_topics')
                        ])
                        @include('public_form.partials.input-checkbox', [
                            'name' => 'explored_background',
                            'label' => 'Explored Background',
                            'checked' => old('explored_background')
                        ])
                    </div>
                    <div class="col-lg-3">
                        @include('public_form.partials.input-checkbox', [
                            'name' => 'written_draft',
                            'label' => 'Written Draft',
                            'checked' => old('written_draft')
                        ])
                        @include('public_form.partials.input-checkbox', [
                            'name' => 'other_learning_outcome',
                            'label' => 'Other Learning Outcome',
                            'checked' => old('other_learning_outcome'),
                            'target' => 'other_learning_outcome_description'
                        ])
                    </div>
                    <div class="col-lg-6">
                        @include('public_form.partials.textarea', [
                            'name' => 'other_learning_outcome_description',
                            'label' => 'Other Learning Outcome',
                            'value' => old('other_learning_outcome_description'),
                            'classes' => 'invisible'
                        ])
                    </div>
                </div>
            </fieldset>

            <fieldset class="on-campus remote asynchronous card-body">
                <div class="row">
                    @include('public_form.partials.textarea', [
                        'name' => 'library_instruction_description',
                        'label' => 'What do you want your students to get out of library instruction?',
                        'value' => old('library_instruction_description'),
                        'classes' => 'col-lg-8',
                        'helptext' => 'Examples: developing a topic, searching effectively, evaluating sources, etc.'
                    ])
                </div>
            </fieldset>

            <fieldset class="on-campus remote asynchronous card-body">
                <div class="row">
                    @include('public_form.partials.textarea', [
                        'name' => 'genai_discussion_interest',
                        'label' => 'Generative AI',
                        'value' => old('genai_discussion_interest'),
                        'classes' => 'col-lg-8',
                        'helptext' => 'If you have class guidelines about ChatGPT, Perplexity, etc., or want to coordinate with your librarian on AI usage, share details here.'
                    ])
                </div>
            </fieldset>

            {{-- ************************
                 Form Footer
            ************************ --}}
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
                <button class="btn btn-warning ml-2" id="clearForm" type="button">Clear Form</button>
            </div>
        </form>
    </div>

@endsection
