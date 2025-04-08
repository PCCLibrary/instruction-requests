@extends('layouts.app')

@section('header')
    <x-breadcrumbs :breadcrumbs="[
        ['label' => 'Instruction Requests', 'route' => 'instructionRequests.index'],
        ['label' => 'Manage Instruction Request']
    ]"/>
    <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
        Manage Instruction Request
    </h1>
@endsection

@section('content')
    @if($instructionRequest->status === 'assigned' && $instructionRequest->detail && ($instructionRequest->detail->assigned_librarian_id == auth()->id()))
        @include('instruction-requests.partials.edit.accept')
    @endif
    
    <script>
        document.addEventListener('livewire:initialized', () => {
            // Listen for the calendar event created event and reload the page
            Livewire.on('googleCalendarEventCreated', (requestId) => {
                // Wait a moment to allow the server to process the status change
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            });
            
            // Listen for the beforeCreateEvent event and update the Livewire component with current form values
            Livewire.on('beforeCreateEvent', () => {
                // Get current form values
                const instructionDateTime = document.getElementById('instruction_datetime').value;
                const instructionDuration = document.getElementById('instruction_duration').value;
                
                console.log('Current form values for calendar event:', {
                    instructionDateTime,
                    instructionDuration
                });
                
                // TODO: Add code to update the Livewire component with these values
                // This would require a method in the Livewire component to receive these values
            });
        });
    </script>

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
        <input type="hidden" name="instruction_requests_id"
               value="{{ $instructionRequest->detail->instruction_requests_id }}">
{{--        <input type="hidden" name="instructor_id" value="{{ $instructionRequest->instructor_id }}">--}}
        {{--        <input type="hidden" name="librarian_id" value="{{ $instructionRequest->librarian_id ?? auth()->id() }}">--}}
        {{--        <input type="hidden" name="campus_id" value="{{ $instructionRequest->campus_id }}">--}}
        <input type="hidden" name="class_id" value="{{ $instructionRequest->class_id }}">
        <input type="hidden" name="created_by" value="{{ $instructionRequest->detail->created_by }}">
        <input type="hidden" name="last_updated_by" value="{{ auth()->user()->display_name }}">

        @include('instruction-requests.partials.edit.edit-header')

        {{-- Editable Fields --}}

        <div class="grid grid-cols-12 gap-6 items-start">
            {{-- Left Column (4 columns) - Always Editable --}}
            <div class="col-span-12 md:col-span-4 items-start">
                {{--                <x-card title="" class="bg-emerald-50 mb-4">--}}
                {{--                    @include('instruction-requests.partials.edit.save')--}}
                {{--                </x-card>--}}

                <x-card title="Status" class="bg-blue-50 mb-4">
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

                {{-- Contact Information --}}
                @include('instruction-requests.partials.edit.instructor-info')

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
        <x-card title="Comments" class="bg-blue-50">
            <x-comments:: :model="$instructionRequest"/>
        </x-card>
    </div>

@endsection
