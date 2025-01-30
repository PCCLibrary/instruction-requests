{{-- resources/views/emails/partials/common_fields.blade.php --}}
<div class="common-fields">
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 8px 0;"><strong>Instructor:</strong></td>
            <td>{{ $request['instructor_name'] ?? 'Not specified' }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0;"><strong>Campus:</strong></td>
            <td>{{ $request['campus_name'] ?? 'Not specified' }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0;"><strong>Instruction Type:</strong></td>
            <td>{{ $request['instruction_type'] ?? 'Not specified' }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0;"><strong>Course:</strong></td>
            <td>
                {{ $request['course_department'] ?? '' }}
                {{ $request['course_number'] ?? '' }}
                @if(!empty($request['course_crn']))
                    (CRN: {{ $request['course_crn'] }})
                @endif
            </td>
        </tr>
        @if(!empty($request['librarian_name']))
            <tr>
                <td style="padding: 8px 0;"><strong>Assigned Librarian:</strong></td>
                <td>{{ $request['librarian_name'] }}</td>
            </tr>
        @endif
    </table>
</div>
