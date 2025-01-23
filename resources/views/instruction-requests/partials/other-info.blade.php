{{-- resources/views/instruction-requests/admin/partials/other-info.blade.php --}}
@if(!empty($instructionRequest->library_instruction_description) || !empty($instructionRequest->genai_discussion_interest))
    <x-card title="Other Information" class="bg-gray-50 mb-4">
        <x-slot name="body">
            <div class="space-y-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                        <tr class="bg-gray-50">
                            @if(!empty($instructionRequest->library_instruction_description))
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Instruction Goals</th>
                            @endif
                            @if(!empty($instructionRequest->genai_discussion_interest))
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">GenAI Discussion Interest</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            @if(!empty($instructionRequest->library_instruction_description))
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $instructionRequest->library_instruction_description }}
                                </td>
                            @endif
                            @if(!empty($instructionRequest->genai_discussion_interest))
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $instructionRequest->genai_discussion_interest }}
                                </td>
                            @endif
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </x-slot>
    </x-card>
@endif
