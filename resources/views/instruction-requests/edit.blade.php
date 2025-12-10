@extends('layouts.app')

@section('page-title', 'Manage Instruction Request')

@section('page-nav')
    <a href="#summary" class="inline-flex items-center gap-1.5 text-cyan-600 border-b-2 border-cyan-600 dark:text-cyan-400 dark:border-cyan-400 py-3">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
        </svg>
        Summary
    </a>
    <a href="#instructor-info" class="inline-flex items-center gap-1.5 text-gray-600 hover:text-cyan-600 dark:text-gray-400 dark:hover:text-cyan-400 py-3">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
        </svg>
        Instructor Info
    </a>
    <a href="#request-info" class="inline-flex items-center gap-1.5 text-gray-600 hover:text-cyan-600 dark:text-gray-400 dark:hover:text-cyan-400 py-3">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" />
        </svg>
        Request Info
    </a>
    <a href="#notes" class="inline-flex items-center gap-1.5 text-gray-600 hover:text-cyan-600 dark:text-gray-400 dark:hover:text-cyan-400 py-3">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
        </svg>
        Notes
    </a>
@endsection

@section('content')
    {{-- Include the lock URL configuration --}}
    @include('instruction-requests.partials.lock-urls')

    {{-- Import edit form-specific lock refresh script --}}
    @push('scripts')
        @vite(['resources/js/edit-lock-refresh.js'])
    @endpush

    @if($instructionRequest->status === 'assigned' && $instructionRequest->detail && ($instructionRequest->detail->assigned_librarian_id == auth()->id()))
        @include('instruction-requests.partials.edit.accept')
    @endif

    <script>
        document.addEventListener('livewire:initialized', () => {
            // Listen for the calendar event created event and reload the page
            // Using Livewire 3 syntax for event listening
            document.addEventListener('googleCalendarEventCreated', (e) => {
                console.log('Google Calendar event created, reloading page');

                // Wait a moment to allow the server to process the status change
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            });

            // Listen for the beforeCreateEvent event and update the Livewire component with current form values
            document.addEventListener('beforeCreateEvent', () => {
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
          class="edit-form"
          data-request-id="{{ $instructionRequest->id }}"
          method="POST"
          enctype="multipart/form-data"
          x-data="{
        isEditing: false,
        instructionType: '{{ $instructionRequest->instruction_type }}',
        initialInstructionDatetime: '{{ $instructionRequest->detail->instruction_datetime }}',
        initialDuration: '{{ $instructionRequest->detail->instruction_duration }}',
        initializeForm() {
            console.log('Form initialized, edit state:', this.isEditing);

            // Initialize the Alpine.js store with improved form state and change tracking
            Alpine.store('formState', {
                // Core state tracking
                isEditing: this.isEditing,
                hasUnsavedChanges: false,

                // Define which sections should be affected by edit toggle
                // Left column sections (status, file uploads, etc.) are deliberately excluded
                editableSections: [
                    'instructorInfo',
                    'requestInfo',
                    'dateTime',
                    'adaProvisions',
                    'learningOutcomes',
                    'instructionGoals'
                ],

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
                },

                // Determine if a specific section should be editable
                isSectionEditable(sectionName) {
                    return this.isEditing && this.editableSections.includes(sectionName);
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

            // Handle form submission - CRITICAL - retains existing submission behavior
            const form = document.getElementById('updateInstructionRequestForm');
            form.addEventListener('submit', (event) => {
                console.log('Form submit event triggered, edit state:', this.isEditing);

                // Temporarily enable all form fields to ensure they can be submitted
                const formFields = form.querySelectorAll('input, select, textarea');
                formFields.forEach(field => {
                    if (field.disabled) {
                        // Mark fields that were disabled so we can restore them later if needed
                        field.setAttribute('data-was-disabled', 'true');
                        field.disabled = false;
                    }
                });

                // Allow form submission regardless of edit state or unsaved changes
                return true;
            });
        },
        toggleEdit() {
            this.isEditing = !this.isEditing;
            Alpine.store('formState').isEditing = this.isEditing;
            console.log('Edit state toggled to:', this.isEditing);

            // Apply visual indication of edit state but don't disable form submission
            const editableFields = document.querySelectorAll('.edit-field');
            editableFields.forEach(field => {
                // Use readonly instead of disabled to allow form submission
                if (field.hasAttribute('readonly')) {
                    field.classList.remove('bg-gray-100', 'cursor-not-allowed');
                } else if (!this.isEditing) {
                    field.classList.add('bg-gray-100', 'cursor-not-allowed');
                }
            });

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
        <input type="hidden" name="class_id" value="{{ $instructionRequest->class_id }}">
        <input type="hidden" name="created_by" value="{{ $instructionRequest->detail->created_by }}">
        <input type="hidden" name="last_updated_by" value="{{ auth()->user()->display_name }}">

        <div id="summary">
            @include('instruction-requests.partials.edit.edit-header')
        </div>

        {{-- Editable Fields --}}

        <div class="grid grid-cols-12 gap-6 items-start">
            {{-- Left Column (4 columns) - Always Editable --}}
            <div class="col-span-12 md:col-span-4 items-start">


                <x-card title="Status"
                        class="bg-blue-50 dark:bg-blue-950 dark:border-gray-700 mb-4"
                        headerclass="dark:text-white"
                >
                    @include('instruction-requests.partials.edit.status')
                </x-card>

                <x-card title="File Attachments"
                        class="bg-blue-50 dark:bg-blue-950 dark:border-gray-700 mb-4"
                        headerclass="dark:text-white"
                >
                    @include('instruction-requests.partials.edit.file-attachments')
                </x-card>

                <x-card title="Tasks"
                        class="bg-blue-50 dark:bg-blue-950 dark:border-gray-700 mb-4"
                        headerclass="dark:text-white"
                >
                    @include('instruction-requests.partials.edit.tasks')
                </x-card>

            </div>

            {{-- Right Column (8 columns) - Toggle Editable --}}
            <div class="col-span-12 md:col-span-8">

                {{-- Contact Information --}}
                <div id="instructor-info">
                    @include('instruction-requests.partials.edit.instructor-info')
                </div>

                {{-- Request Information Card --}}
                <div id="request-info">
                    @include('instruction-requests.partials.edit.request-info')
                </div>

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
                <div id="notes">
                    @include('instruction-requests.partials.edit.notes')
                </div>
            </div>
        </div>
    </form>

    {{-- Comments Section --}}
    <div class="mt-6">
        <x-card title="Comments"
                class="bg-blue-50 dark:bg-blue-950 dark:border-gray-700 mb-4"
                headerclass="dark:text-white"
        >
            <x-comments:: :model="$instructionRequest"/>
        </x-card>
    </div>

    {{-- Reset change tracking when form submission is successful --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check if there's a success message (form was successfully saved)
            if (document.querySelector('.alert-success')) {
                console.log('Form submission was successful, resetting change tracking');
                // Reset change tracking
                if (typeof Alpine !== 'undefined' && Alpine.store('formState')) {
                    Alpine.store('formState').resetChangeTracking();
                }
            }
        });
    </script>

    {{-- Back to Top Button --}}
    <button
        x-data="{ show: false }"
        x-show="show"
        @scroll.window="show = window.pageYOffset > 400"
        @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
        class="fixed bottom-6 right-6 p-3 bg-cyan-600 dark:bg-cyan-500 text-white rounded-full shadow-lg hover:bg-cyan-700 dark:hover:bg-cyan-600 transition z-50"
        style="display: none;"
    >
        ↑
    </button>

    {{-- All model locking functionality moved to lock-refresh.js --}}

@endsection
