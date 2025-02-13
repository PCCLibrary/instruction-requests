{{-- resources/views/instruction-requests/admin/partials/student-progress.blade.php --}}
@php
    $shouldDisplay = $instructionRequest->instruction_type !== 'asynchronous' && (
        $instructionRequest->received_assignment ||
        $instructionRequest->selected_topics ||
        $instructionRequest->explored_background ||
        $instructionRequest->written_draft ||
        $instructionRequest->other_learning_outcome
    );
@endphp

@if($shouldDisplay)
    <x-card title="Students Have:" class="bg-gray-50 mb-4">
        <x-slot name="body">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Received Assignment</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Selected Topics</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Explored Background</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Written Draft</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Other Learning Outcome</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        @foreach(['received_assignment', 'selected_topics', 'explored_background', 'written_draft', 'other_learning_outcome'] as $field)
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-cyan-700">
                                @if($instructionRequest->$field)
                                    <x-heroicon-s-check-circle class="h-5 w-5"/>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                    </tbody>
                </table>
            </div>

            @if(!empty($instructionRequest->other_learning_outcome_description))
                <div class="mt-4 px-6 py-4 bg-gray-50 rounded-md">
                    <h4 class="text-sm font-medium text-gray-900">Other Learning Outcome Description:</h4>
                    <p class="mt-1 text-sm text-gray-600">{{ $instructionRequest->other_learning_outcome_description }}</p>
                </div>
            @endif
        </x-slot>
    </x-card>
@endif
