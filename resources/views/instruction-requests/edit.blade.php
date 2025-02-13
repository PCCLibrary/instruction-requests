@extends('layouts.app')

@section('header')
    <x-breadcrumbs :breadcrumbs="[
        ['label' => 'Instruction Requests', 'route' => 'instructionRequests.index'],
        ['label' => 'Manage Instruction Request']
    ]" />
    <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
        Manage Instruction Request
    </h1>
@endsection

@section('content')
    @if($instructionRequest->status === 'assigned' && $instructionRequest->detail->assigned_librarian_id === auth()->id())
        @include('instruction-requests.partials.edit.accept')
    @endif

    <form action="{{ route('instructionRequests.update', $instructionRequest->id) }}"
          id="updateInstructionRequestForm"
          method="POST"
          enctype="multipart/form-data"
          x-data="{
            isEditing: false,
            instructionType: '{{ $instructionRequest->instruction_type }}',
            initializeForm() {
                console.log('Form initialized, edit state:', this.isEditing);
                Alpine.store('formState', {
                    isEditing: this.isEditing
                });
            },
            toggleEdit() {
                this.isEditing = !this.isEditing;
                Alpine.store('formState').isEditing = this.isEditing;
                console.log('Edit state toggled to:', this.isEditing);

                if (this.isEditing) {
                    this.validateForm();
                }
            },
            validateForm() {
                const form = document.getElementById('updateInstructionRequestForm');
                const studentsInput = form.querySelector('#number_of_students');
                if (studentsInput && studentsInput.value) {
                    const numStudents = parseInt(studentsInput.value);
                    if (numStudents <= 0) {
                        studentsInput.setCustomValidity('Number of students must be greater than 0');
                    } else {
                        studentsInput.setCustomValidity('');
                    }
                }
            },
            updateRequiredFields() {
                console.log('Updating required fields for type:', this.instructionType);

                const asyncField = document.getElementById('asynchronous_instruction_ready_date');
                const preferredField = document.getElementById('preferred_datetime');
                const durationField = document.getElementById('duration');

                if (this.instructionType === 'asynchronous') {
                    asyncField?.setAttribute('required', 'required');
                    preferredField?.removeAttribute('required');
                    durationField?.removeAttribute('required');
                } else {
                    asyncField?.removeAttribute('required');
                    preferredField?.setAttribute('required', 'required');
                    durationField?.setAttribute('required', 'required');
                }
            }
      }"
          x-init="initializeForm()"
          @instruction-type-changed.window="instructionType = $event.detail; updateRequiredFields()"
    >
        @csrf
        @method('PATCH')

        {{-- Essential Hidden Fields --}}
        <input type="hidden" name="instruction_requests_id" value="{{ $instructionRequest->detail->instruction_requests_id }}">
        <input type="hidden" name="instructor_id" value="{{ $instructionRequest->instructor_id }}">
{{--        <input type="hidden" name="librarian_id" value="{{ $instructionRequest->librarian_id ?? auth()->id() }}">--}}
{{--        <input type="hidden" name="campus_id" value="{{ $instructionRequest->campus_id }}">--}}
        <input type="hidden" name="class_id" value="{{ $instructionRequest->class_id }}">
        <input type="hidden" name="created_by" value="{{ $instructionRequest->detail->created_by }}">
        <input type="hidden" name="last_updated_by" value="{{ auth()->user()->display_name }}">

        <div class="grid grid-cols-12 gap-6 items-start">
            {{-- Left Column (4 columns) - Always Editable --}}
            <div class="col-span-12 md:col-span-4 items-start">
                <x-card title="" class="bg-emerald-50 mb-4">
                    @include('instruction-requests.partials.edit.save')
                </x-card>

                <x-card title="Status" class="bg-amber-50 mb-4">
                    @include('instruction-requests.partials.edit.status')
                </x-card>

                <x-card title="File Attachments" class="bg-blue-50 mb-4">
                    @include('instruction-requests.partials.edit.file-attachments')
                </x-card>

                <x-card title="Tasks" class="bg-blue-50 mb-4">
                    @include('instruction-requests.partials.edit.tasks')
                </x-card>

            </div>

            {{-- Right Column (8 columns) - Toggle Editable --}}
            <div class="col-span-12 md:col-span-8">
                {{-- Edit Toggle Button --}}
                <div class="flex justify-end mb-4">
                    <button type="button"
                            @click="toggleEdit"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white"
                            :class="isEditing ? 'bg-green-600 hover:bg-green-700' : 'bg-blue-600 hover:bg-blue-700'"
                    >
                        <template x-if="!isEditing">
                            <svg class="h-4 w-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                            </svg>
                        </template>
                        <template x-if="isEditing">
                            <svg class="h-4 w-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </template>
                        <span x-text="isEditing ? 'Save Changes' : 'Edit Request'"></span>
                    </button>
                </div>

                {{-- Contact Information --}}
                @include('instruction-requests.partials.edit.contact-info')

                {{-- Request Information Card --}}
                @include('instruction-requests.partials.edit.request-info')

                {{-- Date and Time Fields --}}
                @include('instruction-requests.partials.edit.date-time-fields')

                {{-- ADA Provisions --}}
                @include('instruction-requests.partials.edit.ada-provisions')

                {{-- Learning Outcomes --}}
                @if($instructionRequest->instruction_type !== 'asynchronous')
                    @include('instruction-requests.partials.edit.learning-outcomes')
                @endif

                {{-- Instruction Goals --}}
                @include('instruction-requests.partials.edit.instruction-goals')

                {{-- Notes --}}
                @include('instruction-requests.partials.edit.notes')
            </div>
        </div>
    </form>

    {{-- Comments Section --}}
    <div class="mt-6">
{{--        <x-card title="Comments" class="bg-sky-50">--}}
            <x-comments:: :model="$instructionRequest" />
{{--        </x-card>--}}
    </div>
@endsection
