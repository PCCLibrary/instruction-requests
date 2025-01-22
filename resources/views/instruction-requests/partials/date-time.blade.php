{{-- resources/views/instruction-requests/admin/partials/date-time.blade.php --}}
<x-card title="Date and Time Information" class="bg-gray-50">
    <x-slot name="body">
        <div class="overflow-x-auto">
            @if($instructionRequest->instruction_type !== 'asynchronous')
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                    <tr class="bg-gray-50">
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preferred Date & Time</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alternate Date & Time</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($instructionRequest->preferred_datetime)->format('M d, Y g:i A') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($instructionRequest->alternate_datetime)->format('M d, Y g:i A') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $instructionRequest->duration }}
                        </td>
                    </tr>
                    </tbody>
                </table>
            @else
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                    <tr class="bg-gray-50">
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asynchronous Instruction Ready By</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($instructionRequest->asynchronous_instruction_ready_date)->format('F d, Y') }}
                        </td>
                    </tr>
                    </tbody>
                </table>
            @endif

            {{-- Additional Information Table --}}
            @if(!empty($instructionRequest->extra_time_with_class) ||
                !empty($instructionRequest->class_description) ||
                ($instructionRequest->ada_provisions_needed && !empty($instructionRequest->ada_provisions_description)))
                <table class="min-w-full divide-y divide-gray-200 mt-6">
                    <thead>
                    <tr class="bg-gray-50">
                        @if(!empty($instructionRequest->extra_time_with_class))
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Extra Time Needed</th>
                        @endif
                        @if(!empty($instructionRequest->class_description))
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Additional Class Notes</th>
                        @endif
                        @if($instructionRequest->ada_provisions_needed && !empty($instructionRequest->ada_provisions_description))
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ADA Provisions</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        @if(!empty($instructionRequest->extra_time_with_class))
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $instructionRequest->extra_time_with_class }}</td>
                        @endif
                        @if(!empty($instructionRequest->class_description))
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $instructionRequest->class_description }}</td>
                        @endif
                        @if($instructionRequest->ada_provisions_needed && !empty($instructionRequest->ada_provisions_description))
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $instructionRequest->ada_provisions_description }}</td>
                        @endif
                    </tr>
                    </tbody>
                </table>
            @endif
        </div>
    </x-slot>
</x-card>
