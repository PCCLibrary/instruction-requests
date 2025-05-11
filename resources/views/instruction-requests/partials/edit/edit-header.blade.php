<x-card class="bg-teal-50 mb-4 shadow-sm" title="Request summary">
    <div class="p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div>
                <div class="flex items-center mb-2">
                    <x-heroicon-s-calendar class="h-5 w-5 mr-2 text-gray-500" />
                    <span class="font-semibold text-gray-700">Instruction Date:</span>
                </div>
                <p class="text-sm text-gray-900">
                    @if($instructionRequest->detail && $instructionRequest->detail->instruction_datetime)
                        {{ \Carbon\Carbon::parse($instructionRequest->detail->instruction_datetime)->format('M d, Y g:i A') }}
                    @else
                        Not Scheduled
                    @endif
                </p>
            </div>
            <div>
                <div class="flex items-center mb-2">
                    <x-heroicon-o-user-circle class="h-5 w-5 mr-2 text-gray-500" />
                    <span class="font-semibold text-gray-700">Assigned Librarian:</span>
                </div>
                <p class="text-sm text-gray-900">
                    @if($instructionRequest->detail && $instructionRequest->detail->assigned_librarian_id)
                        {{ App\Models\User::find($instructionRequest->detail->assigned_librarian_id)->display_name ?? 'N/A' }}
                    @else
                        Not Assigned
                    @endif
                </p>
            </div>
            <div>
                <div class="flex items-center mb-2">
                    <x-heroicon-o-building-library class="h-5 w-5 mr-2 text-gray-500" />
                    <span class="font-semibold text-gray-700">Campus:</span>
                </div>
                <p class="text-sm text-gray-900">
                    {{ $instructionRequest->campus->name ?? 'N/A' }}
                </p>
            </div>
            <div>
                <div class="flex items-center mb-2">
                    <x-heroicon-o-academic-cap class="h-5 w-5 mr-2 text-gray-500" />
                    <span class="font-semibold text-gray-700">Class Name:</span>
                </div>
                <p class="text-sm text-gray-900">
                    {{ $instructionRequest->department . ' ' . $instructionRequest->course_number }}
                </p>
            </div>
            <div>
                <div class="flex items-center mb-2">
                    <x-heroicon-s-flag class="h-5 w-5 mr-2 text-gray-500" />
                    <span class="font-semibold text-gray-700">Status:</span>
                </div>
                <span class="inline-flex items-center rounded-md px-3 py-1 text-sm font-medium
                    {{ match($instructionRequest->status) {
                        'received' => 'bg-yellow-100 text-yellow-800',
                        'assigned' => 'bg-blue-100 text-blue-800',
                        'accepted' => 'bg-green-100 text-green-800',
                        'scheduled' => 'bg-indigo-100 text-indigo-800',
                        'rejected' => 'bg-red-100 text-red-800',
                        'completed' => 'bg-gray-100 text-gray-800'
                    } }}">
                    {{ ucfirst($instructionRequest->status) }}
                </span>
            </div>
            <div>
                <div class="flex items-center mb-2">
                    <x-heroicon-s-user class="h-5 w-5 mr-2 text-gray-500" />
                    <span class="font-semibold text-gray-700">Instructor:</span>
                </div>
                <p class="text-sm text-gray-900">
                    {{ $instructionRequest->instructor->display_name ?? $instructionRequest->instructor->name ?? 'N/A' }}
                </p>
            </div>
            <div>
                <div class="flex items-center mb-2">
                    <x-heroicon-o-users class="h-5 w-5 mr-2 text-gray-500" />
                    <span class="font-semibold text-gray-700">Students:</span>
                </div>
                <p class="text-sm text-gray-900">
                    {{ $instructionRequest->number_of_students ?? 'N/A' }}
                </p>
            </div>
            <div>
                <div class="flex items-center mb-2">
                    <x-heroicon-o-clock class="h-5 w-5 mr-2 text-gray-500" />
                    <span class="font-semibold text-gray-700">Duration:</span>
                </div>
                <p class="text-sm text-gray-900">
                    {{ $instructionRequest->detail->instruction_duration ?? 'Not Set' }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-6 items-center">
            <div class="col-span-8">
                <x-editor-actions
                    route="{{ route('instructionRequests.index') }}"
                    :showBack="false"
                    :unlockRequest="true"
                />
            </div>
            <div class="col-span-4 flex justify-end">
                @include('instruction-requests.partials.edit.toggle-edit-button')
            </div>
        </div>
    </div>
</x-card>
