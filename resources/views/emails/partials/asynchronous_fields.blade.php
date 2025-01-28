<ul>
    @if (!empty($request['instructor_name']))
        <li><strong>Instructor Name:</strong> {{ $request['instructor_name'] }}</li>
    @endif
    @if (!empty($request['course_crn']))
        <li><strong>Course CRN:</strong> {{ $request['course_crn'] }}</li>
    @endif
    @if (!empty($request['course_number']))
        <li><strong>Course Number:</strong> {{ $request['course_number'] }}</li>
    @endif
    @if (!empty($request['course_department']))
        <li><strong>Department:</strong> {{ $request['course_department'] }}</li>
    @endif
    @if (!empty($request['date_requested']))
        <li><strong>Date Requested:</strong> {{ $request['date_requested'] }}</li>
    @endif
    @if (!empty($request['campus_name']))
        <li><strong>Campus Name:</strong> {{ $request['campus_name'] }}</li>
    @endif
    @if (!empty($request['asynchronous_instruction_ready_date']))
        <li><strong>Asynchronous Instruction Ready Date:</strong> {{ $request['asynchronous_instruction_ready_date'] }}</li>
    @endif
</ul>
