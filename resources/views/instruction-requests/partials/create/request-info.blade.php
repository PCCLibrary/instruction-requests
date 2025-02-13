{{-- resources/views/instruction-requests/partials/create/request-info.blade.php --}}
<x-card title="Request Information" class="bg-gray-50 mb-4">
        <div class="space-y-4">

            <div>
                <x-input-select
                    name="instruction_type"
                    label="Instruction Type"
                    :options="[
                        'on-campus' => 'Librarian joins my class on campus',
                        'remote' => 'Librarian joins my remote class',
                        'asynchronous' => 'Librarian provides resources to be used asynchronously'
                    ]"
                    :selected="old('instruction_type')"
                    help-text="Please select what you need help with."
                    required
                />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">  {{-- 4 columns on medium screens and up --}}
                <div>
                    <x-input-select
                        name="department"
                        label="Subject"
                        :options="$departments"
                        :selected="old('department')"
                        help-text="Choose the subject of your course."
                        required
                    />
                </div>

                <div>
                    <x-input-text
                        name="course_number"
                        label="Course Number"
                        :value="old('course_number')"
                        help-text='Enter the course number (e.g., "122" for course BI 122). Enter "0000" if the course has no number.'
                        required
                    />
                </div>

                <div>
                    <x-input-text
                        name="course_crn"
                        label="Course CRN"
                        :value="old('course_crn')"
                        help-text="Enter the 5-digit CRN for your course. Enter 99999 if the course has no CRN."
                        required
                    />
                </div>

                <div>
                    <x-input-text
                        name="number_of_students"
                        label="Number of Students"
                        type="number"
                        :value="old('number_of_students')"
                        help-text="Enter the number of students in the class."
                        required
                    />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">  {{-- 2 columns on medium screens and up --}}
                <div>
                    <x-input-select
                        name="campus_id"
                        label="Campus"
                        :options="$campuses->pluck('name', 'id')->toArray()"
                    />
                </div>

                <div>
                    <x-input-select
                        name="librarian_id"
                        label="Librarian Preference"
                        :options="$librarians->pluck('display_name', 'id')->toArray()"
                    />
                </div>
            </div>

            <div>
                <x-input-textarea
                    name="desired_student_outcomes"
                    label="Desired Student Outcomes"
                    :value="old('desired_student_outcomes')"
                    help-text="What specific outcomes would you like your students to achieve?"
                />
            </div>

            <div>
                <x-input-textarea
                    name="other_notes"
                    label="Other Notes"
                    :value="old('other_notes')"
                    help-text="Any additional information or special requirements?"
                />
            </div>

        </div>
</x-card>
