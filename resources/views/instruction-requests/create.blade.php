@extends('layouts.app')

@section('header')
    <x-breadcrumbs :breadcrumbs="[
        ['label' => 'Instruction Requests', 'route' => 'instructionRequests.index'],
        ['label' => 'Create New Instruction Request']
    ]" />
    <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
        Create New Instruction Request
    </h1>
@endsection

@section('content')
    <form action="{{ route('instructionRequests.store') }}"
          id="createInstructionRequestForm"
          method="POST"
          enctype="multipart/form-data"
          x-data='{
            instructionType: "",
            init() {
                // Initialize date validation
                Alpine.store("dateValidation", {
                    minDate: null,
                    minTime: null,
                    initializeDateConstraints() {
                        const now = new Date();
                        const tzOffset = -480;
                        const localDate = new Date(now.getTime() + tzOffset * 60000);
                        this.minDate = localDate.toISOString().split("T")[0];
                        this.minTime = localDate.toISOString().slice(0, 16);
                    }
                });
                Alpine.store("dateValidation").initializeDateConstraints();

                // Debug log when instruction type changes
                this.$watch("instructionType", value => {
                    console.log("Instruction type changed to:", value);
                    this.toggleFields(value);
                });
            },
            toggleFields(type) {
                console.log("Toggling fields for type:", type);

                // First hide all field groups
                const allFields = document.querySelectorAll(".on-campus-fields, .remote-fields, .asynchronous-fields");
                allFields.forEach(el => el.classList.add("hidden"));

                // Show the relevant fields based on type
                if (type === "on-campus" || type === "remote") {
                    const syncFields = document.querySelectorAll(".on-campus-fields, .remote-fields");
                    syncFields.forEach(el => el.classList.remove("hidden"));

                    // Set required fields
                    document.getElementById("preferred_datetime")?.setAttribute("required", "required");
                    document.getElementById("duration")?.setAttribute("required", "required");
                    document.getElementById("asynchronous_instruction_ready_date")?.removeAttribute("required");

                    // Disable asynchronous fields
                    document.getElementById("asynchronous_instruction_ready_date")?.setAttribute("disabled", "disabled");
                } else if (type === "asynchronous") {
                    const asyncFields = document.querySelectorAll(".asynchronous-fields");
                    asyncFields.forEach(el => el.classList.remove("hidden"));

                    // Set required fields
                    document.getElementById("asynchronous_instruction_ready_date")?.setAttribute("required", "required");
                    document.getElementById("preferred_datetime")?.removeAttribute("required");
                    document.getElementById("duration")?.removeAttribute("required");

                    // Disable synchronous fields
                    document.getElementById("preferred_datetime")?.setAttribute("disabled", "disabled");
                    document.getElementById("duration")?.setAttribute("disabled", "disabled");
                }
            }
          }'
          x-init="init()"
          @instruction-type-changed.window="instructionType = $event.detail">
        @csrf

        {{-- save the instruction request --}}
        @include('instruction-requests.partials.create.save')

        {{-- Request Information - Moved up to catch instruction type changes --}}
        @include('instruction-requests.partials.create.request-info')

        {{-- Contact Information --}}
        @include('instruction-requests.partials.create.contact-info')

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
    </form>
@endsection
