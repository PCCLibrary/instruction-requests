@php
    $createdBy = old('created_by', $instructionRequest->detail->created_by);
    $lastUpdatedBy = old('last_updated_by', auth()->user()->display_name);
@endphp

<input type="hidden" name="created_by" value="{{ $createdBy }}">
<input type="hidden" name="last_updated_by" value="{{ $lastUpdatedBy }}">

<div x-data="{
    // Admin fields are always editable
    isAlwaysEditable: true,
    handleLibrarianChange() {
        const statusSelect = document.getElementById('status');
        if (statusSelect.value === 'received') {
            statusSelect.value = 'assigned';
        }
        
        // Enhanced console logging for debugging
        const librarianSelect = document.getElementById('assigned_librarian_id');
        const selectedLibrarian = librarianSelect.options[librarianSelect.selectedIndex];
        
        console.log('LIBRARIAN SELECTION CHANGED', {
            librarian_id: librarianSelect.value,
            librarian_name: selectedLibrarian ? selectedLibrarian.text : 'Unknown',
            librarian_element_type: typeof librarianSelect,
            status: statusSelect.value
        });
    }
}">
    <div class="mb-4">
        <label for="status" class="block text-sm font-medium text-gray-700">Request Status</label>
        <select id="status" name="status"
                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            <option value="received" {{ old('status', $instructionRequest->status) === 'received' ? 'selected' : '' }}>
                Received
            </option>
            <option value="assigned" {{ old('status', $instructionRequest->status) === 'assigned' ? 'selected' : '' }}>
                Assigned
            </option>
            <option value="accepted" {{ old('status', $instructionRequest->status) === 'accepted' ? 'selected' : '' }}>
                Accepted
            </option>
            <option value="scheduled" {{ old('status', $instructionRequest->status) === 'scheduled' ? 'selected' : '' }}>
                Scheduled
            </option>
            <option value="completed" {{ old('status', $instructionRequest->status) === 'completed' ? 'selected' : '' }}>
                Completed
            </option>
        </select>
    </div>

    <div class="mb-4">
        <label for="assigned_librarian_id" class="block text-sm font-medium text-gray-700">Assigned Librarian</label>
        <select id="assigned_librarian_id"
                name="assigned_librarian_id"
                @change="handleLibrarianChange()"
                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            @foreach($librarians as $librarian)
                <option value="{{ $librarian->id }}" {{ $instructionRequest->detail->assigned_librarian_id == $librarian->id ? 'selected' : '' }}>
                    {{ $librarian->display_name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <label for="instruction_datetime" class="block text-sm font-medium text-gray-700">Instruction Date & Time</label>
        <input type="datetime-local" id="instruction_datetime" name="instruction_datetime"
               value="{{ old('instruction_datetime', ($instructionRequest->detail->instruction_datetime ?? $instructionRequest->preferred_datetime)?->format('Y-m-d\TH:i')) }}"
               required
               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
    </div>

    <div class="mb-4">
        <label for="instruction_duration" class="block text-sm font-medium text-gray-700">Instruction Duration</label>
        <input type="text" id="instruction_duration" name="instruction_duration"
               value="{{ old('instruction_duration', $instructionRequest->detail->instruction_duration ?? $instructionRequest->duration) }}"
               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
        <p class="mt-2 text-sm text-gray-500">Duration in minutes.</p>
    </div>

    <div class="mb-4">
        @include('instruction-requests.partials.edit.room')
    </div>

    @if($instructionRequest->status == 'accepted' && $instructionRequest->detail->assigned_librarian_id == Auth::user()->id)
        <hr class="mb-6" />
        <div class="my-4" x-data="{ isOpen: false }">
            <button type="button" @click="isOpen = true"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <x-heroicon-o-calendar-date-range class="h-4 w-4 text-white mr-2" />
                Create Google Calendar Event
            </button>

            @include('instruction-requests.partials.edit.gcal')
        </div>
    @endif

    @if($instructionRequest->status == 'scheduled' && $instructionRequest->relationLoaded('googleCalendarEvent') && $instructionRequest->googleCalendarEvent)
        <hr class="mb-6" />
        <div class="my-4">
            <form method="POST" action="{{ route('instructionRequests.deleteCalendarEvent', $instructionRequest->id) }}" 
                  onsubmit="return confirm('Are you sure you want to delete this calendar event? This will set the request status back to accepted.');">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete Calendar Event
                </button>
            </form>
        </div>
    @endif
</div>
