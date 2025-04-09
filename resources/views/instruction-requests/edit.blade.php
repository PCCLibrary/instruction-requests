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
        initialInstructionDatetime: '{{ $instructionRequest->detail->instruction_datetime }}',
        initialDuration: '{{ $instructionRequest->detail->instruction_duration }}',
        initializeForm() {
            console.log('Form initialized, edit state:', this.isEditing);
            
            // Initialize the Alpine.js store with form state and change tracking
            Alpine.store('formState', {
                isEditing: this.isEditing,
                hasUnsavedChanges: false,
                
                // Store initial values for critical fields
                initialValues: {
                    instructionDatetime: document.getElementById('instruction_datetime')?.value || null,
                    instructionDuration: document.getElementById('instruction_duration')?.value || null
                },
                
                // Check if critical scheduling fields have changed
                checkForChanges() {
                    const currentDatetime = document.getElementById('instruction_datetime')?.value || null;
                    const currentDuration = document.getElementById('instruction_duration')?.value || null;
                    
                    this.hasUnsavedChanges = 
                        (currentDatetime !== this.initialValues.instructionDatetime) || 
                        (currentDuration !== this.initialValues.instructionDuration);
                    
                    console.log('Form changes detected:', {
                        hasChanges: this.hasUnsavedChanges,
                        original: this.initialValues,
                        current: { 
                            instructionDatetime: currentDatetime, 
                            instructionDuration: currentDuration 
                        }
                    });
                    
                    return this.hasUnsavedChanges;
                },
                
                // Reset change tracking (to be called after successful form submission)
                resetChangeTracking() {
                    this.hasUnsavedChanges = false;
                    
                    // Update stored initial values to match current values
                    this.initialValues = {
                        instructionDatetime: document.getElementById('instruction_datetime')?.value || null,
                        instructionDuration: document.getElementById('instruction_duration')?.value || null
                    };
                    
                    console.log('Change tracking reset, new initial values:', this.initialValues);
                },
                
                // Check if duration is valid for scheduling
                hasDuration() {
                    const durationInput = document.getElementById('instruction_duration');
                    const duration = durationInput?.value || null;
                    return duration && parseInt(duration) > 0;
                }
            });
            
            // Add event listeners for change detection
            const datetimeInput = document.getElementById('instruction_datetime');
            const durationInput = document.getElementById('instruction_duration');
            
            if (datetimeInput) {
                datetimeInput.addEventListener('input', () => {
                    Alpine.store('formState').checkForChanges();
                });
            }
            
            if (durationInput) {
                durationInput.addEventListener('input', () => {
                    Alpine.store('formState').checkForChanges();
                });
            }
            
            // Handle form submission regardless of edit state
            const form = document.getElementById('updateInstructionRequestForm');
            
            // Add hidden inputs for any disabled fields when the form is submitted
            form.addEventListener('submit', (event) => {
                console.log('Form submit event triggered, edit state:', this.isEditing);
                
                // Process fields that might be disabled but need to be included in the form submission
                form.querySelectorAll('input, select, textarea').forEach(element => {
                    // Skip elements that are already enabled or don't have a name
                    if (!element.disabled || !element.name) return;
                    
                    // Create a hidden input with the same name and value
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = element.name;
                    
                    // Get appropriate value based on input type
                    if (element.type === 'checkbox' || element.type === 'radio') {
                        hiddenInput.value = element.checked ? element.value : '';
                    } else if (element.tagName === 'SELECT' && element.options.length) {
                        hiddenInput.value = element.options[element.selectedIndex].value;
                    } else {
                        hiddenInput.value = element.value;
                    }
                    
                    // Add temporary class for cleanup after submission
                    hiddenInput.classList.add('temp-hidden-input');
                    
                    // Add to form
                    form.appendChild(hiddenInput);
                    console.log(`Added hidden input for disabled field: ${element.name}=${hiddenInput.value}`);
                });
                
                // Form will continue submission normally
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

    {{-- Reset change tracking and clean up temporary elements when form submission is successful --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check if there's a success message (form was successfully saved)
            if (document.querySelector('.alert-success')) {
                console.log('Form submission was successful, resetting change tracking');
                
                // Reset change tracking
                if (typeof Alpine !== 'undefined' && Alpine.store('formState')) {
                    Alpine.store('formState').resetChangeTracking();
                }
                
                // Clean up any temporary hidden input elements
                document.querySelectorAll('.temp-hidden-input').forEach(el => {
                    el.remove();
                });
            }
        });
    </script>

@endsection
