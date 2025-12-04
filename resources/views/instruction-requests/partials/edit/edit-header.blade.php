<x-card class="bg-teal-50 dark:bg-teal-950 mb-4 shadow-sm dark:border-gray-700"
        title="Request summary"
        headerclass="dark:text-white">
    <div class="p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div>
                <div class="flex items-center mb-2">
                    <x-heroicon-s-calendar class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-400" />
                    <span class="font-semibold text-gray-700 dark:text-gray-300">Instruction Date:</span>
                </div>
                <p class="text-sm text-gray-900 dark:text-gray-200">
                    @if($instructionRequest->detail && $instructionRequest->detail->instruction_datetime)
                        {{ \Carbon\Carbon::parse($instructionRequest->detail->instruction_datetime)->format('M d, Y g:i A') }}
                    @else
                        Not Scheduled
                    @endif
                </p>
            </div>
            <div>
                <div class="flex items-center mb-2">
                    <x-heroicon-o-user-circle class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-400" />
                    <span class="font-semibold text-gray-700 dark:text-gray-300">Assigned Librarian:</span>
                </div>
                <p class="text-sm text-gray-900 dark:text-gray-200">
                    @if($instructionRequest->detail && $instructionRequest->detail->assigned_librarian_id)
                        {{ App\Models\User::find($instructionRequest->detail->assigned_librarian_id)->display_name ?? 'N/A' }}
                    @else
                        Not Assigned
                    @endif
                </p>
            </div>
            <div>
                <div class="flex items-center mb-2">
                    <x-heroicon-o-building-library class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-400" />
                    <span class="font-semibold text-gray-700 dark:text-gray-300">Campus:</span>
                </div>
                <p class="text-sm text-gray-900 dark:text-gray-200">
                    {{ $instructionRequest->campus->name ?? 'N/A' }}
                </p>
            </div>
            <div>
                <div class="flex items-center mb-2">
                    <x-heroicon-o-academic-cap class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-400" />
                    <span class="font-semibold text-gray-700 dark:text-gray-300">Class Name:</span>
                </div>
                <p class="text-sm text-gray-900 dark:text-gray-200">
                    {{ $instructionRequest->department . ' ' . $instructionRequest->course_number }}
                </p>
            </div>
            <div>
                <div class="flex items-center mb-2">
                    <x-heroicon-s-flag class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-400" />
                    <span class="font-semibold text-gray-700 dark:text-gray-300">Status:</span>
                </div>
                <span class="inline-flex items-center rounded-md px-3 py-1 text-sm font-medium
                    {{ match($instructionRequest->status) {
                        'received' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                        'assigned' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                        'accepted' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                        'scheduled' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
                        'in_progress' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
                        'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                        'completed' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                        default => 'bg-gray-300 text-gray-900 dark:bg-gray-600 dark:text-gray-100'
                    } }}">
                    {{ ucwords(str_replace('_', ' ', $instructionRequest->status)) }}
                </span>
            </div>
            <div>
                <div class="flex items-center mb-2">
                    <x-heroicon-s-user class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-400" />
                    <span class="font-semibold text-gray-700 dark:text-gray-300">Instructor:</span>
                </div>
                <p class="text-sm text-gray-900 dark:text-gray-200">
                    {{ $instructionRequest->instructor->display_name ?? $instructionRequest->instructor->name ?? 'N/A' }}
                </p>
            </div>
            <div>
                <div class="flex items-center mb-2">
                    <x-heroicon-o-users class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-400" />
                    <span class="font-semibold text-gray-700 dark:text-gray-300">Students:</span>
                </div>
                <p class="text-sm text-gray-900 dark:text-gray-200">
                    {{ $instructionRequest->number_of_students ?? 'N/A' }}
                </p>
            </div>
            <div>
                <div class="flex items-center mb-2">
                    <x-heroicon-o-clock class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-400" />
                    <span class="font-semibold text-gray-700 dark:text-gray-300">Duration:</span>
                </div>
                <p class="text-sm text-gray-900 dark:text-gray-200">
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
