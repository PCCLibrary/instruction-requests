{{-- components/multiselect.blade.php --}}
@props([
    'name',
    'label',
    'options' => [],
    'selected' => [],
    'placeholder' => 'Select options...',
    'helptext' => null,
    'required' => false,
    'searchable' => true,
    'disabled' => false
])

@php
    // Ensure selected is an array and convert to strings for comparison
    $selectedValues = is_array($selected) ? array_map('strval', $selected) : [];
    $optionsArray = is_array($options) ? $options : [];
@endphp

<div class="space-y-1"
     x-data="{
        // State
        open: false,
        searchTerm: '',
        selectedItems: {{ Js::from(collect($selectedValues)->map(fn($id) => ['value' => $id, 'label' => $optionsArray[$id] ?? 'Unknown'])->values()) }},
        options: {{ Js::from(collect($optionsArray)->map(fn($label, $id) => ['value' => strval($id), 'label' => $label])->values()) }},

        // Computed
        get filteredOptions() {
            return this.options.filter(option => {
                const notSelected = !this.selectedItems.some(selected => selected.value === option.value);
                const matchesSearch = !this.searchTerm ||
                    option.label.toLowerCase().includes(this.searchTerm.toLowerCase());
                return notSelected && matchesSearch;
            });
        },

        get selectedValues() {
            return this.selectedItems.map(item => item.value);
        },

        get hasSelection() {
            return this.selectedItems.length > 0;
        },

        // Methods
        addItem(value, label) {
            if (!this.selectedItems.some(item => item.value === value)) {
                this.selectedItems.push({ value, label });
            }
            this.closeDropdown();
        },

        removeItem(value) {
            this.selectedItems = this.selectedItems.filter(item => item.value !== value);
        },

        openDropdown() {
            this.open = true;
            this.$nextTick(() => {
                this.$refs.searchInput?.focus();
            });
        },

        closeDropdown() {
            this.open = false;
            this.searchTerm = '';
        },

        handleKeydown(event) {
            if (event.key === 'Escape') {
                this.closeDropdown();
            } else if (event.key === 'Enter' && this.filteredOptions.length > 0) {
                event.preventDefault();
                const firstOption = this.filteredOptions[0];
                this.addItem(firstOption.value, firstOption.label);
            } else if (event.key === 'Backspace' && !this.searchTerm && this.selectedItems.length > 0) {
                const lastItem = this.selectedItems[this.selectedItems.length - 1];
                this.removeItem(lastItem.value);
            }
        },

        init() {
            // Close dropdown when clicking outside
            this.$el.addEventListener('click', (e) => e.stopPropagation());
            document.addEventListener('click', () => this.closeDropdown());
        }
     }"
     @keydown.window="handleKeydown($event)">

    <!-- Label -->
    @if($label)
        <x-input-label :value="$label" :for="$name" :required="$required" />
    @endif

    <!-- Hidden inputs for form submission (array format) -->
    <div x-ref="hiddenInputs">
        <template x-for="value in selectedValues" :key="value">
            <input type="hidden" :name="'{{ $name }}[]'" :value="value" />
        </template>
    </div>

    <!-- Main container -->
    <div class="relative"
         :class="{ 'opacity-50 pointer-events-none': {{ $disabled ? 'true' : 'false' }} }">

        <!-- Input trigger -->
        <div class="relative">
            <input type="text"
                   x-ref="searchInput"
                   x-model="searchTerm"
                   @click="openDropdown()"
                   @focus="openDropdown()"
                   @keydown="handleKeydown($event)"
                   :placeholder="hasSelection ? 'Add more...' : '{{ $placeholder }}'"
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:placeholder-gray-400 text-gray-900 bg-white"
                   x-bind:disabled="$store.formState?.isEditing === false" />

            <!-- Dropdown arrow -->
            <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                <svg class="w-4 h-4 text-gray-400"
                     :class="{ 'rotate-180': open }"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </div>

        <!-- Selected items (pills) -->
        <div x-show="hasSelection"
             class="flex flex-wrap gap-2 mt-2"
             x-cloak>
            <template x-for="item in selectedItems" :key="item.value">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-sky-600 text-white dark:bg-sky-700">
                    <span x-text="item.label"></span>
                    <button type="button"
                            @click="removeItem(item.value)"
                            class="ml-1.5 inline-flex items-center justify-center w-4 h-4 rounded-full text-sky-200 hover:text-white hover:bg-sky-800 focus:outline-none focus:bg-sky-800 focus:text-white"
                            :aria-label="'Remove ' + item.label">
                        <svg class="w-2 h-2" stroke="currentColor" fill="none" viewBox="0 0 8 8">
                            <path stroke-linecap="round" stroke-width="1.5" d="m1 1 6 6m0-6-6 6"/>
                        </svg>
                    </button>
                </span>
            </template>
        </div>

        <!-- Dropdown -->
        <div x-show="open && filteredOptions.length > 0"
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="transform opacity-0 scale-95"
             x-transition:enter-end="transform opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="transform opacity-100 scale-100"
             x-transition:leave-end="transform opacity-0 scale-95"
             class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-800 shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
             x-cloak>

            <template x-for="option in filteredOptions" :key="option.value">
                <div @click="addItem(option.value, option.label)"
                     class="cursor-pointer select-none relative py-2 pl-3 pr-9 text-gray-900 dark:text-gray-100 hover:bg-blue-50 dark:hover:bg-gray-700">
                    <span x-text="option.label" class="block font-normal truncate"></span>
                </div>
            </template>

            <!-- Empty state when search has no results -->
            <div x-show="filteredOptions.length === 0"
                 class="relative cursor-default select-none py-2 pl-3 pr-9 text-gray-500 dark:text-gray-400"
                 x-cloak>
                No options found
            </div>
        </div>

        <!-- Empty state when no search but all options selected -->
        <div x-show="open && !searchTerm && filteredOptions.length === 0 && options.length > 0"
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="transform opacity-0 scale-95"
             x-transition:enter-end="transform opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="transform opacity-100 scale-100"
             x-transition:leave-end="transform opacity-0 scale-95"
             class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-800 shadow-lg rounded-md py-3 text-base ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
             x-cloak>
            <div class="text-center text-gray-500 dark:text-gray-400 text-sm">
                All options selected
            </div>
        </div>
    </div>

    <!-- Help text -->
    @if($helptext)
        <x-helptext :name="$name" :helptext="$helptext" />
    @endif
</div>
