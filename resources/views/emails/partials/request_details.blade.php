<ul>
    @if (!empty($request['instructor_name']))
        <li><strong>Instructor:</strong> {{ $request['instructor_name'] }}</li>
    @endif

    @if (!empty($request['course_department']) && !empty($request['course_number']))
        <li><strong>Course:</strong> {{ $request['course_department'] }} {{ $request['course_number'] }}
            @if (!empty($request['course_crn']))
                (CRN: {{ $request['course_crn'] }})
            @endif
        </li>
    @endif

    @if (!empty($request['campus_name']))
        <li><strong>Campus:</strong> {{ $request['campus_name'] }}</li>
    @endif

    @if (!empty($request['date_requested']))
        <li><strong>Date Requested:</strong> {{ $request['date_requested'] }}</li>
    @endif

    @if (!empty($request['preferred_datetime']))
        <li><strong>Preferred Date/Time:</strong> {{ $request['preferred_datetime'] }}</li>
    @endif

    @if (!empty($request['alternate_datetime']))
        <li><strong>Alternate Date/Time:</strong> {{ $request['alternate_datetime'] }}</li>
    @endif
</ul>
