{{-- /views/instruction-requests/partials/create/course-details.blade.php --}}
<x-fieldset legend="Course Details" classes="bg-white dark:bg-gray-800 dark:border-gray-700 on-campus-fields remote-fields asynchronous-fields">
    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <x-validated-input-select
                    name="department"
                    id="department"
                    label="Subject"
                    :options="$departments"
                    :selected="old('department')"
                    help-text="Choose the subject of your course."
                    required
                    :validation="[
                        'alwaysRequired' => true,
                        'messages' => [
                            'required' => 'Subject is required'
                        ]
                    ]"
                />
            </div>

            <div>
                <x-validated-input-text
                    name="course_number"
                    id="course_number"
                    label="Course Number"
                    :value="old('course_number')"
                    help-text='Enter the course number (e.g., "122" for course BI 122). Enter "0000" if the course has no number.'
                    required
                    :validation="[
                        'alwaysRequired' => true,
                        'customValidator' => 'validateCourseNumber',
                        'messages' => [
                            'required' => 'Course number is required',
                            'invalid' => 'Please enter a valid course number (e.g., 101, 102A, 20B)'
                        ]
                    ]"
                />
            </div>

            <div>
                <x-validated-input-text
                    name="course_crn"
                    id="course_crn"
                    label="Course CRN"
                    :value="old('course_crn')"
                    help-text="Enter the 5-digit CRN for your course. Enter 99999 if the course has no CRN."
                    required
                    :validation="[
                        'alwaysRequired' => true,
                        'customValidator' => 'validateCRN',
                        'messages' => [
                            'required' => 'CRN is required',
                            'invalid' => 'Please enter a 5-digit CRN number (numbers only)'
                        ]
                    ]"
                />
            </div>

            <div>
                <x-validated-input-text
                    name="number_of_students"
                    classes="on-campus-fields remote-fields"
                    id="number_of_students"
                    label="Number of Students"
                    type="number"
                    :value="old('number_of_students')"
                    help-text="Enter the number of students in the class."
                    :validation="[
                        'requiredFor' => ['on-campus', 'remote', 'asynchronous'],
                        'customValidator' => 'validatePositiveInteger',
                        'messages' => [
                            'required' => 'Number of students is required',
                            'invalid' => 'Please enter a whole number (integers only)',
                            'min' => 'Please enter 1 or more students'
                        ]
                    ]"
                />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <x-validated-input-select
                    name="campus_id"
                    id="campus_id"
                    label="Campus"
                    :options="$campuses->pluck('name', 'id')->toArray()"
                    :selected="old('campus_id')"
                    help-text="Select the location where your class takes place or is assigned to."
                    required
                    :validation="[
                        'alwaysRequired' => true,
                        'messages' => [
                            'required' => 'Campus preference is required'
                        ]
                    ]"
                />
            </div>

            <div>
                <x-input-select
                    name="librarian_id"
                    id="librarian_id"
                    label="Librarian Preference"
                    :options="$librarians->pluck('display_name', 'id')->toArray()"
                    :selected="old('librarian_id')"
                    help-text="We will try to assign your preferred librarian, but we can't guarantee their availability."
                />
            </div>
        </div>
    </div>
</x-fieldset>
