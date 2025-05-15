{{-- resources/views/instruction-requests/partials/course-details.blade.php --}}
<x-card class="bg-gray-50 dark:bg-gray-800 dark:border-gray-700 mb-4">
    <x-slot name="title">
        <div class="flex justify-between items-center">
            <h5 class="mb-0 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Request Information</h5>
        </div>
    </x-slot>

    <x-slot name="body">
        <div class="p-4">
            <div class="space-y-4" x-data="{
                get isDisabled() {
                    return !$store.formState.isSectionEditable('requestInfo');
                }
            }">
                <div>
                    <x-input-select
                        name="instruction_type"
                        label="Instruction Type"
                        :options="[
                            'on-campus' => 'Librarian joins my class on campus',
                            'remote' => 'Librarian joins my online (scheduled meeting) class',
                            'asynchronous' => 'Librarian provides resources to be used asynchronously'
                        ]"
                        :selected="$instructionRequest->instruction_type"
                        helptext="Please select what you need help with."
                        class="edit-field"
                        x-bind:class="isDisabled ? 'bg-gray-100 dark:bg-gray-700 cursor-not-allowed' : ''"
                    />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-select
                            name="department"
                            label="Subject"
                            :options="$departments"
                            :selected="$instructionRequest->department"
                            helptext="Choose the subject of your course."
                            class="edit-field"
                            x-bind:class="isDisabled ? 'bg-gray-100 dark:bg-gray-700 cursor-not-allowed' : ''"
                        />
                    </div>

                    <div>
                        <x-input-text
                            name="course_number"
                            label="Course Number"
                            :value="$instructionRequest->course_number"
                            helptext='Enter the course number (e.g., "122" for course BI 122). Enter "0000" if the course has no number.'
                            class="edit-field"
                            x-bind:readonly="isDisabled"
                            x-bind:class="isDisabled ? 'bg-gray-100 dark:bg-gray-700 cursor-not-allowed' : ''"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-text
                            name="course_crn"
                            label="Course CRN"
                            :value="$instructionRequest->course_crn"
                            helptext="Enter the 5-digit CRN for your course. Enter 99999 if the course has no CRN."
                            class="edit-field"
                            x-bind:readonly="isDisabled"
                            x-bind:class="isDisabled ? 'bg-gray-100 dark:bg-gray-700 cursor-not-allowed' : ''"
                        />
                    </div>

                    <div>
                        <x-input-text
                            name="number_of_students"
                            label="Number of Students"
                            type="number"
                            :value="$instructionRequest->number_of_students"
                            helptext="Enter the number of students in the class."
                            class="edit-field"
                            x-bind:readonly="isDisabled"
                            x-bind:class="isDisabled ? 'bg-gray-100 dark:bg-gray-700 cursor-not-allowed' : ''"
                        />
                    </div>
                </div>

                <x-input-select
                    name="campus_id"
                    label="Campus"
                    :options="$campuses->pluck('name', 'id')->toArray()"
                    :selected="$instructionRequest->campus_id"
                    class="col-lg-6 edit-field"
                    x-bind:class="isDisabled ? 'bg-gray-100 dark:bg-gray-700 cursor-not-allowed' : ''"
                />

                <x-input-select
                    name="librarian_id"
                    label="Librarian Preference"
                    :options="$librarians->pluck('display_name', 'id')->toArray()"
                    :selected="$instructionRequest->librarian_id"
                    class="col-lg-6 edit-field"
                    x-bind:class="isDisabled ? 'bg-gray-100 dark:bg-gray-700 cursor-not-allowed' : ''"
                />

                <div class="">
                    <x-input-textarea
                        name="assignment_description"
                        label="Assignment Description"
                        :value="$instructionRequest->assignment_description"
                        class="edit-field"
                        helptext="Assignment description."
                        x-bind:readonly="isDisabled"
                        x-bind:class="isDisabled ? 'bg-gray-100 dark:bg-gray-700 cursor-not-allowed' : ''"
                    />


                </div>
            </div>
        </div>
    </x-slot>
</x-card>
