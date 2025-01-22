@php
    $assignedLibrarianId = old('assigned_librarian_id', $instructionRequest->assigned_librarian_id);
    $createdBy = old('created_by', $instructionRequest->detail->created_by);
    $lastUpdatedBy = old('last_updated_by', auth()->user()->display_name);
@endphp

<input type="hidden" name="assigned_librarian_id" value="{{ $assignedLibrarianId }}">
<input type="hidden" name="created_by" value="{{ $createdBy }}">
<input type="hidden" name="last_updated_by" value="{{ $lastUpdatedBy }}">

<div class="mb-4">
    <label for="status" class="block text-sm font-medium text-gray-700">Request Status</label>
    <select id="status" name="status" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        <option value="received" {{ old('status', $instructionRequest->status) === 'received' ? 'selected' : '' }}>Received</option>
        <option value="assigned" {{ old('status', $instructionRequest->status) === 'assigned' ? 'selected' : '' }}>Assigned</option>
        <option value="accepted" {{ old('status', $instructionRequest->status) === 'accepted' ? 'selected' : '' }}>Accepted</option>
        <option value="completed" {{ old('status', $instructionRequest->status) === 'completed' ? 'selected' : '' }}>Completed</option>
    </select>
</div>

<div class="mb-4">
    <label for="assigned_librarian_id" class="block text-sm font-medium text-gray-700">Assigned Librarian</label>
    <select id="assigned_librarian_id" name="assigned_librarian_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        @foreach($librarians as $librarian)
            <option value="{{ $librarian->id }}" {{ $instructionRequest->detail->assigned_librarian_id == $librarian->id ? 'selected' : '' }}>
                {{ $librarian->display_name }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-4">
    <label for="instruction_datetime" class="block text-sm font-medium text-gray-700">Instruction Date & Time</label>
    <input type="datetime-local" id="instruction_datetime" name="instruction_datetime" value="{{ old('instruction_datetime', $instructionRequest->detail->instruction_datetime ?? $instructionRequest->preferred_datetime) }}" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
</div>

<div class="mb-4">
    <label for="instruction_duration" class="block text-sm font-medium text-gray-700">Instruction Duration</label>
    <input type="text" id="instruction_duration" name="instruction_duration" value="{{ old('instruction_duration', $instructionRequest->detail->instruction_duration ?? $instructionRequest->duration) }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
    <p class="mt-2 text-sm text-gray-500">Duration in minutes.</p>
</div>

@if($instructionRequest->status == 'accepted' && $instructionRequest->detail->assigned_librarian_id == Auth::user()->id)
    <div class="my-4" x-data="{ isOpen: false }">
        <button type="button" @click="isOpen = true" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Create Google Calendar Event
        </button>
        <div x-show="isOpen" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Schedule Calendar Event</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">Placeholder for the google event booking form.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" @click="isOpen = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6">
        <p>Open Google Calendar in new window</p>
        <ul class="mt-2 space-y-1">
            @foreach($campuses as $campus)
                <li>
                    @if($campus->gcal)
                        <a href="{{ $campus->gcal }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">{{ $campus->name }}</a>
                    @else
                        {{ $campus->name }}
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</div>

@include('instruction-requests.admin.tasks')
