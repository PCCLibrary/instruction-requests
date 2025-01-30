{{-- resources/views/emails/partials/asynchronous_fields.blade.php --}}
<div class="type-specific-fields">
    <h3 style="margin: 15px 0;">Asynchronous Instruction Details</h3>
    <table style="width: 100%; border-collapse: collapse;">
        @if(!empty($request['asynchronous_instruction_ready_date']))
            <tr>
                <td style="padding: 8px 0;"><strong>Materials Needed By:</strong></td>
                <td>{{ $request['asynchronous_instruction_ready_date'] }}</td>
            </tr>
        @endif
    </table>
</div>
