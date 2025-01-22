{{-- resources/views/instruction-requests/admin/partials/request-info.blade.php --}}
<x-card class="bg-gray-50">
    <x-slot name="title">
        <div class="flex justify-between items-center">
            <h5 class="text-base font-medium leading-6 text-gray-900">Request Information</h5>
            <button type="button"
                    x-data="editToggle"
                    @click="toggleEdit"
                    class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-cyan-700 hover:bg-cyan-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500">Edit Request &nbsp;
                <x-heroicon-s-pencil-square class="h-4 w-4" />
                <span x-text="isEditing ? 'Save' : 'Edit'"></span>
            </button>
        </div>
    </x-slot>

    <x-slot name="body">
        <div class="p-4 space-y-6">
            <div class="space-y-4">
                <x-input-select
                    name="instruction_type"
                    label="Instruction Type"
                    :options="[
                        'on-campus' => 'Librarian joins my class on campus',
                        'remote' => 'Librarian joins my remote class',
                        'asynchronous' => 'Librarian provides resources to be used asynchronously'
                    ]"
                    :selected="$instructionRequest->instruction_type"
                    helptext="Please select what you need help with."
                    class="edit-field"
                    disabled="true"
                />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-input-select
                        name="department"
                        label="Subject"
                        :options="$departments"
                        :selected="$instructionRequest->department"
                        helptext="Choose the subject of your course."
                        class="edit-field"
                        disabled="true"
                    />

                    <x-input-text
                        name="course_number"
                        label="Course Number"
                        :value="$instructionRequest->course_number"
                        helptext='Enter the course number (e.g., "122" for course BI 122). Enter "0000" if the course has no number.'
                        class="edit-field"
                        disabled="true"
                    />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-input-text
                        name="course_crn"
                        label="Course CRN"
                        :value="$instructionRequest->course_crn"
                        helptext="Enter the 5-digit CRN for your course. Enter 99999 if the course has no CRN."
                        class="edit-field"
                        disabled="true"
                    />

                    <x-input-text
                        name="number_of_students"
                        label="Number of Students"
                        type="number"
                        :value="$instructionRequest->number_of_students"
                        helptext="Enter the number of students in the class."
                        class="edit-field"
                        disabled="true"
                    />
                </div>
            </div>
        </div>
    </x-slot>

    <x-slot name="footer">
        <div class="space-y-6">
            @if(isset($instructionRequest->librarian->display_name) || isset($instructionRequest->campus->name))
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if($instructionRequest->librarian->display_name)
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">Requested librarian:</h4>
                            <p class="mt-1 text-sm text-gray-600">{{ $instructionRequest->librarian->display_name }}</p>
                        </div>
                    @endif

                    @if($instructionRequest->campus->name)
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">Campus:</h4>
                            <p class="mt-1 text-sm text-gray-600">{{ $instructionRequest->campus->name }}</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </x-slot>
</x-card>
