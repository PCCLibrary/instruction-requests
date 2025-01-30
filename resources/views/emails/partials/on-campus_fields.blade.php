{{-- resources/views/emails/partials/on-campus_fields.blade.php --}}
<div class="type-specific-fields">
    <h3 style="margin: 15px 0;">On-Campus Session Details</h3>
    <table style="width: 100%; border-collapse: collapse;">
        @if(!empty($request['preferred_datetime']))
            <tr>
                <td style="padding: 8px 0;"><strong>Preferred Date & Time:</strong></td>
                <td>{{ $request['preferred_datetime'] }}</td>
            </tr>
        @endif
        @if(!empty($request['alternate_datetime']))
            <tr>
                <td style="padding: 8px 0;"><strong>Alternate Date & Time:</strong></td>
                <td>{{ $request['alternate_datetime'] }}</td>
            </tr>
        @endif
        @if(!empty($request['number_of_students']))
            <tr>
                <td style="padding: 8px 0;"><strong>Number of Students:</strong></td>
                <td>{{ $request['number_of_students'] }}</td>
            </tr>
        @endif
        @if(!empty($request['duration']))
            <tr>
                <td style="padding: 8px 0;"><strong>Duration:</strong></td>
                <td>{{ $request['duration'] }}</td>
            </tr>
        @endif
    </table>
</div>
