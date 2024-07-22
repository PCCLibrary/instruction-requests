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
    @if (!empty($request->preferred_datetime))
        <li><strong>Preferred Date & Time:</strong> {{ \Carbon\Carbon::parse($request->preferred_datetime)->format('m/d/Y H:i') }}</li>
    @endif
    @if (!empty($request->alternate_datetime))
        <li><strong>Alternate Date & Time:</strong> {{ \Carbon\Carbon::parse($request->alternate_datetime)->format('m/d/Y H:i') }}</li>
    @endif
    @if (!empty($request->number_of_students))
        <li><strong>Number of Students:</strong> {{ $request->number_of_students }}</li>
    @endif
    @if (!empty($request->duration))
        <li><strong>Duration:</strong> {{ $request->duration }}</li>
    @endif
</ul>
