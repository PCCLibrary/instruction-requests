<ul>
    @if (!empty($request->instructor_name))
        <li><strong>Instructor Name:</strong> {{ $request->instructor_name }}</li>
    @endif
    @if (!empty($request->course_crn))
        <li><strong>Course CRN:</strong> {{ $request->course_crn }}</li>
    @endif
    @if (!empty($request->course_number))
        <li><strong>Course Number:</strong> {{ $request->course_number }}</li>
    @endif
    @if (!empty($request->department))
        <li><strong>Department:</strong> {{ $request->department }}</li>
    @endif
    @if (!empty($request->date_requested))
        <li><strong>Date Requested:</strong> {{ \Carbon\Carbon::parse($request->date_requested)->format('m/d/Y') }}</li>
    @endif
    @if (!empty($request->campus_name))
        <li><strong>Campus Name:</strong> {{ $request->campus_name }}</li>
    @endif
    @if (!empty($request->asynchronous_instruction_ready_date))
        <li><strong>Asynchronous Instruction Ready Date:</strong> {{ \Carbon\Carbon::parse($request->asynchronous_instruction_ready_date)->format('m/d/Y') }}</li>
    @endif
</ul>
