@props(['instructionRequest', 'details' => false])

<ul>
    {{-- Instruction Request Fields --}}
    @isset($instructionRequest->instruction_type)
        <li><strong>Instruction Type:</strong> {{ ucfirst($instructionRequest->instruction_type) }}</li>
    @endisset

    @isset($instructionRequest->campus)
        <li><strong>Campus:</strong> {{ $instructionRequest->campus->name }}</li>
    @endisset

    @isset($instructionRequest->librarian)
        <li><strong>Librarian:</strong> {{ $instructionRequest->librarian->full_name }}</li>
    @endisset

    @isset($instructionRequest->department)
        <li><strong>Department:</strong> {{ $instructionRequest->department }}</li>
    @endisset

    @isset($instructionRequest->course_number)
        <li><strong>Course Number:</strong> {{ $instructionRequest->course_number }}</li>
    @endisset

    @isset($instructionRequest->course_crn)
        <li><strong>Course CRN:</strong> {{ $instructionRequest->course_crn }}</li>
    @endisset

    @isset($instructionRequest->number_of_students)
        <li><strong>Number of Students:</strong> {{ $instructionRequest->number_of_students }}</li>
    @endisset

    @isset($instructionRequest->preferred_datetime)
        <li><strong>Preferred Datetime:</strong> {{ \Carbon\Carbon::parse($instructionRequest->preferred_datetime)->format('d/m/Y h:i A') }}</li>
    @endisset

    @isset($instructionRequest->alternate_datetime)
        <li><strong>Alternate Datetime:</strong> {{ \Carbon\Carbon::parse($instructionRequest->alternate_datetime)->format('d/m/Y h:i A') }}</li>
    @endisset

    @isset($instructionRequest->duration)
        <li><strong>Duration:</strong> {{ $instructionRequest->duration }}</li>
    @endisset

    @isset($instructionRequest->status)
        <li><strong>Status:</strong> {{ ucfirst($instructionRequest->status) }}</li>
    @endisset

    {{-- Conditionally Show Instruction Request Details --}}
    @if($details && $instructionRequest->instructionRequestDetails)
        @isset($instructionRequest->instructionRequestDetails->instruction_datetime)
            <li><strong>Instruction Datetime:</strong> {{ \Carbon\Carbon::parse($instructionRequest->instructionRequestDetails->instruction_datetime)->format('d/m/Y h:i A') }}</li>
        @endisset

        @isset($instructionRequest->instructionRequestDetails->instruction_duration)
            <li><strong>Instruction Duration:</strong> {{ $instructionRequest->instructionRequestDetails->instruction_duration }}</li>
        @endisset

        @isset($instructionRequest->instructionRequestDetails->class_notes)
            <li><strong>Class Notes:</strong> {{ $instructionRequest->instructionRequestDetails->class_notes }}</li>
        @endisset

        @isset($instructionRequest->instructionRequestDetails->assessment_notes)
            <li><strong>Assessment Notes:</strong> {{ $instructionRequest->instructionRequestDetails->assessment_notes }}</li>
        @endisset

        {{-- Additional fields from instructionRequestDetails if needed --}}
    @endif
</ul>
