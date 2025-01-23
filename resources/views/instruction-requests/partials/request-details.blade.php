{{-- Hidden fields for form submission --}}
@foreach(['instruction_type', 'department', 'course_number', 'course_crn', 'number_of_students'] as $field)
    <input type="hidden" name="{{ $field }}" value="{{ $instructionRequest->$field ?? null }}">
@endforeach

{{-- Contact Information Section --}}
@include('instruction-requests.partials.contact-info', ['instructionRequest' => $instructionRequest])

{{-- Request Information Section --}}
@include('instruction-requests.partials.request-info', ['instructionRequest' => $instructionRequest, 'departments' => $departments])

{{-- Course Materials Section --}}

{{-- Date and Time Section --}}
@include('instruction-requests.partials.date-time', ['instructionRequest' => $instructionRequest])

{{-- Students Progress Section --}}
@include('instruction-requests.partials.student-progress', ['instructionRequest' => $instructionRequest])

{{-- Other Information Section --}}
@include('instruction-requests.partials.other-info', ['instructionRequest' => $instructionRequest])

{{-- Hidden Fields --}}
@foreach([
    'instruction_requests_id' => $instructionRequest->detail->instruction_requests_id,
    'instruction_type' => $instructionRequest->instruction_type,
    'instructor_id' => $instructionRequest->instructor_id,
    'librarian_id' => $instructionRequest->librarian_id ?? 2,
    'campus_id' => $instructionRequest->campus_id ?? 1,
    'preferred_datetime' => $instructionRequest->preferred_datetime,
    'alternate_datetime' => $instructionRequest->alternate_datetime,
    'duration' => $instructionRequest->duration,
    'asynchronous_instruction_ready_date' => $instructionRequest->asynchronous_instruction_ready_date
] as $name => $value)
    <input type="hidden" name="{{ $name }}" value="{{ $value }}">
@endforeach
