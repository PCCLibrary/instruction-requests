@extends('emails.base')

@section('content')
    <p>This request has been assigned to {{ $request['librarian_name'] }}.</p>

    <div style="margin: 20px 0; padding: 15px; background-color: #f8f9fa; border-radius: 4px;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 8px 0; width: 180px;"><strong>Duration:</strong></td>
                <td>{{ $request['duration'] }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0;"><strong>Number of Students:</strong></td>
                <td>{{ $request['number_of_students'] }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; vertical-align: top;"><strong>Instruction Goals:</strong></td>
                <td>{{ $request['instruction_goals'] }}</td>
            </tr>
            @if(!empty($request['genai_discussion_interest']))
            <tr>
                <td style="padding: 8px 0; vertical-align: top;"><strong>AI Discussion:</strong></td>
                <td>{{ $request['genai_discussion_interest'] }}</td>
            </tr>
            @endif
            @if(!empty($request['assignment_description']))
            <tr>
                <td style="padding: 8px 0; vertical-align: top;"><strong>Assignment Description:</strong></td>
                <td>{{ $request['assignment_description'] }}</td>
            </tr>
            @endif
            @if(!empty($request['other_notes']))
            <tr>
                <td style="padding: 8px 0; vertical-align: top;"><strong>Additional Notes:</strong></td>
                <td>{{ $request['other_notes'] }}</td>
            </tr>
            @endif
        </table>
    </div>

    <p style="margin: 20px 0;">
        <a href="{{ $dashboardUrl }}" class="button">View Request Details</a>
    </p>
@endsection
