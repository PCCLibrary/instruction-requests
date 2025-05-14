@extends('layouts.app')

@section('header')
    <x-breadcrumbs :breadcrumbs="[
        ['label' => 'Instruction Requests', 'route' => 'instructionRequests.index'],
        ['label' => 'Create New Instruction Request']
    ]"/>
    <h1 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:text-3xl sm:truncate">
        Create New Instruction Request
    </h1>
@endsection

@section('content')
    <form action="{{ route('instructionRequests.store') }}"
          id="createInstructionRequestForm"
          method="POST"
          enctype="multipart/form-data"
          x-data="{
              instructionType: '{{ old('instruction_type', '') }}',

              instructionTypeSettings: {
                  'on-campus': {
                      required: ['number_of_students', 'campus_id', 'preferred_datetime', 'duration'],
                      notRequired: ['librarian_id', 'asynchronous_instruction_ready_date', 'alternate_datetime'],
                      disable: ['asynchronous_instruction_ready_date']
                  },
                  'remote': {
                      required: ['librarian_id', 'preferred_datetime', 'duration', 'campus_id'],
                      notRequired: ['number_of_students', 'asynchronous_instruction_ready_date', 'alternate_datetime'],
                      disable: ['asynchronous_instruction_ready_date']
                  },
                  'asynchronous': {
                      required: ['asynchronous_instruction_ready_date', 'campus_id'],
                      notRequired: ['librarian_id', 'number_of_students', 'preferred_datetime', 'alternate_datetime', 'duration'],
                      disable: ['librarian_id', 'preferred_datetime', 'alternate_datetime', 'duration']
                  }
              },

              toggleFieldVisibility() {
                  // Hide all fields first
                  const allFields = document.querySelectorAll('.on-campus-fields, .remote-fields, .asynchronous-fields');
                  allFields.forEach(field => field.classList.add('hidden'));

                  // Show fields for selected type
                  if (this.instructionType) {
                      const fieldsToShow = document.querySelectorAll(`.${this.instructionType}-fields`);
                      fieldsToShow.forEach(field => field.classList.remove('hidden'));
                  }
              },

              updateFieldRequirements() {
                  if (!this.instructionType) return;

                  const settings = this.instructionTypeSettings[this.instructionType];
                  if (!settings) return;

                  // Handle required fields
                  settings.required.forEach(id => {
                      const field = document.getElementById(id);
                      if (field) field.setAttribute('required', 'required');
                  });

                  // Handle non-required fields
                  settings.notRequired.forEach(id => {
                      const field = document.getElementById(id);
                      if (field) field.removeAttribute('required');
                  });

                  // Handle disabled fields
                  settings.disable.forEach(id => {
                      const field = document.getElementById(id);
                      if (field) field.setAttribute('disabled', 'disabled');
                  });
              },

              init() {
               // Set up the date validation store
                    Alpine.store('dateValidation', {
                        minDate: new Date().toISOString().split('T')[0],  // For date-only fields
                        minTime: new Date().toISOString().slice(0, 16)    // For datetime-local fields
                    });
                  this.toggleFieldVisibility();
                  this.updateFieldRequirements();

                  this.$watch('instructionType', () => {
                      this.toggleFieldVisibility();
                      this.updateFieldRequirements();
                  });
              }
          }"
          x-init="init"
    >
        @csrf

        @include('instruction-requests.partials.create.create-header')

        <x-card title="Request Information" class="bg-white">
            <div class="space-y-6">
                {{-- These sections are always visible --}}
                @include('instruction-requests.partials.create.instructor-info')

                <x-fieldset legend="" class="bg-white">
                    <div class="space-y-6">
                        <x-input-select
                            name="instruction_type"
                            id="instruction_type"
                            label="Instruction Type"
                            :options="[
                                'on-campus' => 'Librarian joins my class on campus',
                                'remote' => 'Librarian joins my remote class',
                                'asynchronous' => 'Librarian provides resources to be used asynchronously'
                            ]"
                            :selected="old('instruction_type')"
                            help-text="Please select what you need help with."
                            required
                            x-model="instructionType"
                        />
                    </div>
                </x-fieldset>

                {{-- All sections below use visibility classes --}}
                @include('instruction-requests.partials.create.course-details')
                @include('instruction-requests.partials.create.scheduling')
                @include('instruction-requests.partials.create.async-schedule')
                @include('instruction-requests.partials.create.file-attachments')
                @include('instruction-requests.partials.create.learning-outcomes')
                @include('instruction-requests.partials.create.instruction-goals')
                @include('instruction-requests.partials.create.ada-provisions')
                @include('instruction-requests.partials.create.notes')
            </div>
        </x-card>
    </form>
@endsection
