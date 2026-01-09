@php
    $createdBy = old('created_by', $instructionRequest->detail->created_by);
    $lastUpdatedBy = old('last_updated_by', auth()->user()->display_name);
@endphp

<input type="hidden" name="created_by" value="{{ $createdBy }}">
<input type="hidden" name="last_updated_by" value="{{ $lastUpdatedBy }}">

<div x-data="{
    handleLibrarianChange() {
        const statusSelect = document.getElementById('status');
        if (statusSelect.value === 'received') {
            statusSelect.value = 'assigned';
        }
    }
}">
    <div class="mb-4">
        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Request Status</label>
        <select id="status" name="status"
                class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:text-white">
            <option value="received" {{ old('status', $instructionRequest->status) === 'received' ? 'selected' : '' }}>
                Received
            </option>
            <option value="assigned" {{ old('status', $instructionRequest->status) === 'assigned' ? 'selected' : '' }}>
                Assigned
            </option>
            <option value="accepted" {{ old('status', $instructionRequest->status) === 'accepted' ? 'selected' : '' }} disabled>
                Accepted
            </option>
            <option value="rejected" {{ old('status', $instructionRequest->status) === 'rejected' ? 'selected' : '' }} disabled>
                Rejected
            </option>
            <option value="in_progress" {{ old('status', $instructionRequest->status) === 'in_progress' ? 'selected' : '' }} disabled>
                In Progress
            </option>
            <option value="completed" {{ old('status', $instructionRequest->status) === 'completed' ? 'selected' : '' }}>
                Completed
            </option>
        </select>
    </div>

    <div class="mb-4">
        <label for="assigned_librarian_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Assigned Librarian</label>
        <select id="assigned_librarian_id"
                name="assigned_librarian_id"
                @change="handleLibrarianChange()"
                class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:text-white">
            @foreach($librarians as $librarian)
                <option value="{{ $librarian->id }}" {{ $instructionRequest->detail->assigned_librarian_id == $librarian->id ? 'selected' : '' }}>
                    {{ $librarian->display_name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <label for="instruction_datetime" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Instruction Date & Time</label>
        <input type="datetime-local" id="instruction_datetime" name="instruction_datetime"
               value="{{ old('instruction_datetime', ($instructionRequest->detail->instruction_datetime ?? $instructionRequest->preferred_datetime)?->format('Y-m-d\TH:i')) }}"
               required
               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
    </div>

    <div class="mb-4">
        <label for="instruction_duration" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Instruction Duration</label>
        <input type="text" id="instruction_duration" name="instruction_duration"
               value="{{ old('instruction_duration', $instructionRequest->detail->instruction_duration ?? $instructionRequest->duration) }}"
               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Duration in minutes.</p>
    </div>

    <div class="mb-4">
        @include('instruction-requests.partials.edit.room')
    </div>

    @if($instructionRequest->status == 'accepted' && $instructionRequest->detail->assigned_librarian_id == Auth::user()->id && $instructionRequest->instruction_type !== 'asynchronous')
        <hr class="mb-6 dark:border-gray-700" />
        <div class="my-4"
            x-data="{
                isOpen: false,

                // Method to check if button should be disabled
                checkDisabled() {
                    // Check for unsaved changes
                    const hasChanges = Alpine.store('formState')?.hasUnsavedChanges || false;

                    // Check for valid duration
                    const hasDuration = Alpine.store('formState')?.hasDuration() || false;

                    return hasChanges || !hasDuration;
                }
            }"
            @close-modal.window="isOpen = false">
            <button
                type="button"
                @click="if(!checkDisabled()) {
                    // Clear any existing toast messages
                    const toasts = document.querySelectorAll('.toast-message');
                    toasts.forEach(toast => toast.remove());

                    isOpen = true;
                }"
                :class="{
                    'opacity-50 cursor-not-allowed': checkDisabled(),
                    'hover:bg-indigo-700': !checkDisabled()
                }"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
                <x-heroicon-o-calendar-date-range class="h-4 w-4 text-white mr-2" />
                Create Google Calendar Event
            </button>

            <!-- Warning messages for disabled state -->
            <div x-show="checkDisabled()" class="mt-2 text-sm text-amber-600 dark:text-amber-400" x-cloak>
                <div x-show="Alpine.store('formState')?.hasUnsavedChanges">
                    Please save changes before scheduling.
                </div>
                <div x-show="!Alpine.store('formState')?.hasDuration()">
                    Please enter a valid duration before scheduling.
                </div>
            </div>

            @include('instruction-requests.partials.edit.gcal')
        </div>
    @endif

    @if($instructionRequest->status == 'in_progress')
        <hr class="mb-6 dark:border-gray-700" />
        <div class="my-4">
            @if($instructionRequest->googleCalendarEvent && $instructionRequest->googleCalendarEvent->html_link)
                <a href="{{ $instructionRequest->googleCalendarEvent->html_link }}"
                   target="_blank"
                   class="inline-flex items-center px-4 py-2 mb-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <x-heroicon-o-link class="h-4 w-4 text-white mr-2" />
                    Open in Google Calendar
                </a>
            @endif
                @livewire('delete-google-calendar-event', ['requestId' => $instructionRequest->id])
        </div>
    @endif

    {{-- Mark In Progress Button for Asynchronous Requests --}}
    @if($instructionRequest->status == 'accepted' &&
        $instructionRequest->detail->assigned_librarian_id == Auth::user()->id &&
        $instructionRequest->instruction_type === 'asynchronous')
        <hr class="mb-6 dark:border-gray-700" />
        <div class="my-4">
            @livewire('mark-in-progress-button', ['requestId' => $instructionRequest->id])
        </div>
    @endif
</div>
