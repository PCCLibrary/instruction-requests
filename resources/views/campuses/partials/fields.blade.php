<!-- resources/views/campuses/partials/status.blade.php -->
<div class="grid grid-cols-1 gap-6 mt-4 sm:grid-cols-2">
    <div>
        <x-input-text name="name" label="Campus Name"
                      value="{{ old('name', $campus?->name ?? '') }}"
                      required />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <div>
        <x-input-text name="code" label="Campus Code"
                      value="{{ old('code', $campus?->code ?? '') }}"
                      required />
        <x-input-error class="mt-2" :messages="$errors->get('code')" />
    </div>

    <div class="mt-4" x-data="{
        gcalValue: '{{ old('gcal', $campus?->gcal ?? '') }}',
        gcalValid: null,

        validateGcal() {
            if (!this.gcalValue || this.gcalValue.trim() === '') {
                this.gcalValid = null; // Empty is valid (optional field)
                return;
            }

            const pattern = /^[a-z0-9._]+@group\.calendar\.google\.com$/;
            this.gcalValid = pattern.test(this.gcalValue.trim());
        },

        init() {
            this.validateGcal();
            this.$watch('gcalValue', () => this.validateGcal());
        }
    }">
        <label for="gcal" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Google Calendar ID
        </label>
        <div class="mt-1 relative">
            <input
                type="text"
                name="gcal"
                id="gcal"
                x-model="gcalValue"
                @blur="validateGcal()"
                class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400 sm:text-sm"
                :class="{
                    'pr-10': gcalValid !== null,
                    'border-red-300 dark:border-red-500 text-red-900 dark:text-red-300 placeholder-red-300 dark:placeholder-red-400 focus:border-red-500 focus:ring-red-500': gcalValid === false,
                    'border-green-300 dark:border-green-500 text-green-900 dark:text-green-300 placeholder-green-300 dark:placeholder-green-400 focus:border-green-500 focus:ring-green-500': gcalValid === true
                }"
                placeholder="[calendar-id]@group.calendar.google.com"
            />

            <!-- Validation Icons -->
            <div x-show="gcalValid !== null" class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none" x-cloak>
                <!-- Success Icon -->
                <svg x-show="gcalValid === true" class="h-5 w-5 text-green-500 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20" x-cloak>
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>

                <!-- Error Icon -->
                <svg x-show="gcalValid === false" class="h-5 w-5 text-red-500 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20" x-cloak>
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
        </div>

        <!-- Validation Messages -->
        <p x-show="gcalValid === false" class="mt-2 text-sm text-red-600 dark:text-red-400" x-cloak>
            Invalid format. Expected: [calendar-id]@group.calendar.google.com
        </p>
        <p x-show="gcalValid === true" class="mt-2 text-sm text-green-600 dark:text-green-400" x-cloak>
            Valid Google Calendar ID format
        </p>
        <p x-show="gcalValid === null && gcalValue && gcalValue.trim() === ''" class="mt-2 text-sm text-gray-500 dark:text-gray-400" x-cloak>
            Leave empty if no Google Calendar integration needed
        </p>
        <p x-show="gcalValid === null && (!gcalValue || gcalValue.trim() === '')" class="mt-2 text-sm text-gray-500 dark:text-gray-400" x-cloak>
            Google Calendar key for this campus. This is used to sync with the library calendar.
        </p>
        <x-input-error class="mt-2" :messages="$errors->get('gcal')" />
    </div>

    <div class="sm:col-span-2">
        <x-multiselect
            name="librarian_ids"
            label="Send notifications to"
            :options="$librarians"
            :selected="old('librarian_ids', $campus?->librarian_ids ?? [])"
            placeholder="Click to add librarians"
            helptext="Librarians to receive notifications from requests at this location." />
        <x-input-error class="mt-2" :messages="$errors->get('librarian_ids')" />
    </div>
</div>
