<div>
    <x-fieldset legend="Instructor">
        <x-row>
            {{-- Instructor Name --}}
            <div class="col-lg-6">
                <label for="name">Instructor Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="formData.name" id="name" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-lg-6">
                <label for="display_name">Students refer to me as</label>
                <input type="text" class="form-control @error('display_name') is-invalid @enderror" wire:model="formData.display_name" id="display_name">
                @error('display_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

        </x-row>

        <x-row>
            {{-- Pronouns --}}
            <div class="col-lg-4">
                <label for="pronouns">Pronouns</label>
                <input type="text" class="form-control @error('pronouns') is-invalid @enderror" wire:model="formData.pronouns" id="pronouns">
                @error('pronouns')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-lg-4">
                <label for="email">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" wire:model="formData.email" id="email" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-lg-4">
                <label for="phone">Phone</label>
                <input type="text" class="form-control @error('phone') is-invalid @enderror" wire:model="formData.phone" id="phone">
                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </x-row>
    </x-fieldset>

    <x-fieldset legend="Type of Instruction">
        <x-row>
            {{-- Instruction Type Field --}}
            <div class="col-lg-6">
                <label for="instruction_type">Instruction Type</label>
                <select class="form-control @error('instruction_type') is-invalid @enderror" wire:model="formData.instruction_type" id="instruction_type" required>
                    <option value="">Select Instruction Type</option>
                    <option value="Librarian joins my class on campus">Librarian joins my class on campus</option>
                    <option value="Librarian joins my remote class">Librarian joins my remote class</option>
                    <option value="Librarian provides resources to be used asynchronously">Librarian provides resources to be used asynchronously</option>
                </select>
                @error('instruction_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

        </x-row>
    </x-fieldset>

    <x-fieldset legend="Location">
        <x-row>
            {{-- Librarian Preference Field --}}
            <div class="col-lg-6">
                <label for="librarian_id">Librarian Preference</label>
                <select class="form-control @error('librarian_id') is-invalid @enderror" wire:model="formData.librarian_id" id="librarian_id" required>
                    <option value="">Select Librarian</option>
                    @foreach($librarians as $librarian)
                        <option value="{{ $librarian->id }}">{{ $librarian->display_name }}</option>
                    @endforeach
                </select>
                <small class="form-text text-muted">Do you want to work with a specific librarian or a librarian from a specific campus? We will make every effort to assign your preferred librarian, but we can't guarantee their availability.</small>
                @error('librarian_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Campus ID Field --}}
            <div class="col-lg-6">
                <label for="campus_id">Class Location</label>
                <select class="form-control @error('campus_id') is-invalid @enderror" wire:model="formData.campus_id" id="campus_id" required>
                    <option value="">Select Campus</option>
                    @foreach($campuses as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
                <small class="form-text text-muted">Choose the campus with which you are primarily associated (if online).</small>
                @error('campus_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </x-row>
    </x-fieldset>

    <x-fieldset legend="Class Information">
        <x-row>
            {{-- Department Field --}}
            <div class="col-lg-4">
                <label for="department">Subject</label>
                <select class="form-control @error('department') is-invalid @enderror" wire:model="formData.department" id="department" required>
                    <option value="">Select Subject</option>
                    @foreach($departments as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
                <small class="form-text text-muted">Choose the subject of your course.</small>
                @error('department')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Course Number Field --}}
            <div class="col-lg-3">
                <label for="course_number">Course Number</label>
                <input type="text" class="form-control @error('course_number') is-invalid @enderror" wire:model="formData.course_number" id="course_number" required placeholder="e.g., '122' for BI 122">
                <small class="form-text text-muted">Enter the course number (e.g., "122" for course BI 122). Enter "0000" if the course has no number.</small>
                @error('course_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Course CRN Field --}}
            <div class="col-lg-3">
                <label for="course_crn">Course CRN</label>
                <input type="text" class="form-control @error('course_crn') is-invalid @enderror" wire:model="formData.course_crn" id="course_crn" required placeholder="Enter the 5-digit CRN">
                <small class="form-text text-muted">Enter the 5-digit CRN for your course. Enter 99999 if the course has no CRN.</small>
                @error('course_crn')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Number of Students Field --}}
            <div class="col-lg-2">
                <label for="number_of_students">Number of Students</label>
                <input type="number" class="form-control @error('number_of_students') is-invalid @enderror" wire:model="formData.number_of_students" id="number_of_students">
                <small class="form-text text-muted">Enter the number of students in the class.</small>
                @error('number_of_students')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </x-row>

        <x-row>
            {{-- class_syllabus (File Upload) --}}
            <div class="col-lg-6">
                <label for="class_syllabus">Attach syllabus (doc, pdf, or txt)</label>
                <input type="file" class="form-control @error('class_syllabus') is-invalid @enderror" wire:model="formData.class_syllabus" id="class_syllabus" multiple>
                <small class="form-text text-muted">Please attach the class syllabus.</small>
                @error('class_syllabus')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- class_description --}}
            <div class="col-lg-6">
                <label for="class_description">Describe your class</label>
                <textarea class="form-control @error('class_description') is-invalid @enderror" wire:model="formData.class_description" id="class_description" rows="3"></textarea>
                <small class="form-text text-muted">Describe your class (this needs a prompt).</small>
                @error('class_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </x-row>
    </x-fieldset>

    <x-fieldset legend="ADA Information" >
        <x-row>
            {{-- ADA Provisions Needed Checkbox --}}
            <div class="form-group col-lg-3">
                <label for="ada_provisions_needed">ADA Provisions Needed</label>
                <input type="checkbox" class="form-control @error('ada_provisions_needed') is-invalid @enderror" id="ada_provisions_needed" wire:model="formData.ada_provisions_needed">
                @error('ada_provisions_needed')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- ADA Provisions Description Textarea --}}
            @if($formData['ada_provisions_needed'] ?? false)
                <div class="form-group col-lg-9">
                    <label for="ada_provisions_description">Describe the ADA accommodations needed for your class.</label>
                    <textarea id="ada_provisions_description" class="form-control @error('ada_provisions_description') is-invalid @enderror" wire:model="formData.ada_provisions_description" rows="3"></textarea>
                    @error('ada_provisions_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            @endif
        </x-row>
    </x-fieldset>

    <x-fieldset legend="Date, Time, and Duration">
        <x-row>
            {{-- Preferred Datetime Field --}}
            <div class="form-group col-lg-4">
                <label for="preferred_datetime">Preferred Date & Time</label>
                <input type="datetime-local" class="form-control @error('preferred_datetime') is-invalid @enderror" id="preferred_datetime" wire:model="formData.preferred_datetime">
                <small class="form-text text-muted">Enter the date and time you would prefer to have your instruction session.</small>
                @error('preferred_datetime')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Alternate Datetime Field --}}
            <div class="form-group col-lg-4">
                <label for="alternate_datetime">Alternate Date & Time</label>
                <input type="datetime-local" class="form-control @error('alternate_datetime') is-invalid @enderror" id="alternate_datetime" wire:model="formData.alternate_datetime">
                <small class="form-text text-muted">Enter an alternate date and time for your instruction session.</small>
                @error('alternate_datetime')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Duration Field --}}
            <div class="form-group col-lg-4">
                <label for="duration">Duration</label>
                <input type="text" class="form-control @error('duration') is-invalid @enderror" id="duration" wire:model="formData.duration">
                <small class="form-text text-muted">Enter the length of instruction you would like your class to receive, in minutes.</small>
                @error('duration')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </x-row>
    </x-fieldset>

    <x-fieldset legend="Extra Time">
        <x-row>
            {{-- Extra Time With Class Field --}}
            <div class="form-group col-lg-8">
                <label for="extra_time_with_class">Do you need time to discuss non-library matters with your class on the day of library instruction?</label>
                <textarea class="form-control @error('extra_time_with_class') is-invalid @enderror" id="extra_time_with_class" rows="3" wire:model="formData.extra_time_with_class"></textarea>
                @error('extra_time_with_class')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </x-row>
    </x-fieldset>

    <x-fieldset legend="Asynchronous Date">
        <x-row>
            <div class="col-lg-6">
                <label for="asynchronous_instruction_ready_date">Asynchronous instruction ready by</label>
                <input type="date" id="asynchronous_instruction_ready_date" wire:model.defer="formData.asynchronous_instruction_ready_date" class="form-control @error('asynchronous_instruction_ready_date') is-invalid @enderror">
                <span class="help-block">Examples of asynchronous instruction include the development of tutorials, videos, research guides, having a librarian embedded in your Brightspace classroom to support students, and more.</span>
                @error('asynchronous_instruction_ready_date')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </x-row>
    </x-fieldset>

    <x-fieldset legend="Assignments">
        <x-row>
            <div class="col-lg-6">
                <label for="instructor_attachments">Attach documents (doc, pdf, or txt)</label>
                <input type="file" id="instructor_attachments" wire:model.defer="formData.instructor_attachments" multiple class="form-control @error('instructor_attachments') is-invalid @enderror">
                <span class="help-block">Upload class assignment(s) and other relevant documents.</span>
                @error('instructor_attachments')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-lg-6">
                <label for="assignment_description">Assignment description and sample topics</label>
                <textarea id="assignment_description" wire:model.defer="formData.assignment_description" class="form-control @error('assignment_description') is-invalid @enderror" rows="3"></textarea>
                @error('assignment_description')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </x-row>
    </x-fieldset>

    <x-fieldset legend="By the time students receive library instruction they will have:">
        <x-row>
            <div class="col-lg-3">
                {{-- Received Assignment Checkbox --}}
                <label for="received_assignment">Received Assignment</label>
                <input type="checkbox" id="received_assignment" wire:model.lazy="formData.received_assignment" />
                @error('received_assignment') <span class="error">{{ $message }}</span> @enderror

                {{-- Selected Topics Checkbox --}}
                <label for="selected_topics">Selected Topics</label>
                <input type="checkbox" id="selected_topics" wire:model.lazy="formData.selected_topics" />
                @error('selected_topics') <span class="error">{{ $message }}</span> @enderror

                {{-- Explored Background Checkbox --}}
                <label for="explored_background">Explored Background</label>
                <input type="checkbox" id="explored_background" wire:model.lazy="formData.explored_background" />
                @error('explored_background') <span class="error">{{ $message }}</span> @enderror

                {{-- Written Draft Checkbox --}}
                <label for="written_draft">Written Draft</label>
                <input type="checkbox" id="written_draft" wire:model.lazy="formData.written_draft" />
                @error('written_draft') <span class="error">{{ $message }}</span> @enderror

                {{-- Other Learning Outcome Checkbox --}}
                <label for="other_learning_outcome">Other Learning Outcome</label>
                <input type="checkbox" id="other_learning_outcome" wire:model.lazy="formData.other_learning_outcome" />
                @error('other_learning_outcome') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="col-lg-9">
                {{-- Other Learning Outcome Description Textarea --}}
                @if($formData['other_learning_outcome'] ?? false)
                    <label for="other_learning_outcome_description">Other Learning Outcome Description</label>
                    <textarea id="other_learning_outcome_description" wire:model.lazy="formData.other_learning_outcome_description" class="{{ $formData['other_learning_outcome'] ? '' : 'invisible' }}"></textarea>
                    @error('other_learning_outcome_description') <span class="error">{{ $message }}</span> @enderror
                @endif
            </div>
        </x-row>
    </x-fieldset>

    <x-fieldset>
        <x-row>
            <label for="library_instruction_description">What do you want your students to get out of library instruction?</label>
            <textarea id="library_instruction_description" wire:model.lazy="formData.library_instruction_description" class="form-control col-lg-9"></textarea>
            @error('library_instruction_description') <span class="error">{{ $message }}</span> @enderror
        </x-row>
    </x-fieldset>

    <x-fieldset>
        <x-row>
            <label for="genai_discussion_interest">Generative AI Discussion Interest</label>
            <textarea id="genai_discussion_interest" wire:model.lazy="formData.genai_discussion_interest" class="form-control col-lg-9"></textarea>
            @error('genai_discussion_interest') <span class="error">{{ $message }}</span> @enderror
        </x-row>
    </x-fieldset>

    <x-fieldset>
        <x-row class="mt-4">
            <!-- Submit Button -->
            <button wire:click.prevent="submit" class="btn btn-primary">Submit</button>

            <!-- Clear Form Button -->
            <button wire:click.prevent="clearForm" class="btn btn-warning ml-2">Clear Form</button>
        </x-row>
    </x-fieldset>


</div>
