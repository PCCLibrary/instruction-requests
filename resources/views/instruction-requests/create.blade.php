@extends('layouts.app')

@section('header')
    <x-breadcrumbs :breadcrumbs="[
        ['label' => 'Instruction Requests', 'route' => 'instructionRequests.index'],
        ['label' => 'Create Instruction Request']
    ]" />
    <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
        Create Instruction Request
    </h1>
@endsection

@section('content')
    <form action="{{ route('instructionRequests.store') }}"
          id="createInstructionRequestForm"
          method="POST"
          enctype="multipart/form-data"
          x-data="{
            instructionType: '',
            initializeForm() {
                // Initialize date validation
                Alpine.store('dateValidation', {
                    minDate: null,
                    minTime: null,
                    initializeDateConstraints() {
                        const now = new Date();
                        const tzOffset = -480; // UTC-8 (Los Angeles)
                        const localDate = new Date(now.getTime() + tzOffset * 60000);
                        this.minDate = localDate.toISOString().split('T')[0];
                        this.minTime = localDate.toISOString().slice(0, 16);
                    }
                });
                Alpine.store('dateValidation').initializeDateConstraints();
            },
            updateRequiredFields() {
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

        {{-- save the instruction request --}}
        @include('instruction-requests.partials.create.save')

        {{-- Contact Information --}}
        @include('instruction-requests.partials.create.contact-info')

        {{-- Request Information --}}
        @include('instruction-requests.partials.create.request-info')

        {{-- Date and Time Fields --}}
        @include('instruction-requests.partials.create.date-time-fields')

        {{-- File Attachments --}}
        @include('instruction-requests.partials.create.file-attachments')

        {{-- ADA Provisions --}}
        @include('instruction-requests.partials.create.ada-provisions')

        {{-- Learning Outcomes --}}
        @include('instruction-requests.partials.create.learning-outcomes')

        {{-- Instruction Goals --}}
        @include('instruction-requests.partials.create.instruction-goals')

        {{-- Notes --}}
        @include('instruction-requests.partials.create.notes')

        {{-- Submit Button --}}
{{--        <div class="mt-6 flex justify-end">--}}
{{--            <x-button type="submit" class="bg-blue-600 hover:bg-blue-700">--}}
{{--                Create Instruction Request--}}
{{--            </x-button>--}}
{{--        </div>--}}
    </form>
@endsection
