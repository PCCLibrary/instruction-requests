<!-- resources/views/campuses/partials/status.blade.php -->
<div class="grid grid-cols-1 gap-6 mt-4 sm:grid-cols-2">
    <div>
        <x-input-text name="name" label="Campus Name"
                      value="{{ old('name', $campus?->name ?? '') }}"
                      required />
    </div>

    <div>
        <x-input-text name="code" label="Campus Code"
                      value="{{ old('code', $campus?->code ?? '') }}"
                      required />
    </div>

    <div>
        <x-input-text name="gcal" label="Google Calendar Key"
                      value="{{ old('gcal', $campus?->gcal ?? '') }}"
        />
        <p class="mt-2 text-sm text-gray-500">Google Calendar key for this campus. This is used to sync with the library calendar.</p>
    </div>

    <div class="sm:col-span-2">
        <label for="librarian_ids" class="block text-sm font-medium text-gray-700">Send notifications to:</label>
        <select
            name="librarian_ids[]"
            id="librarian_ids"
            multiple
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 choices-librarian-select"
        >
            @foreach($librarians as $id => $name)
                <option value="{{ $id }}"
                    @selected(old('librarian_ids', $campus?->librarian_ids ?? []) && in_array($id, old('librarian_ids', is_array($campus?->librarian_ids) ? $campus?->librarian_ids : [])))>
                    {{ $name }}
                </option>
            @endforeach
        </select>
        <p class="mt-2 text-sm text-gray-500">Librarians to receive notifications from requests at this location.</p>
    </div>
</div>
