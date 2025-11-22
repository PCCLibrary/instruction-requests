{{-- Searchable Select Server Component - Server-side filtering only --}}
@props([
    'name',
    'label',
    'placeholder' => 'Search...',
    'valueProperty',
    'searchProperty',
    'options' => [],
    'selectedLabel' => null,
    'valueField' => 'id',
    'labelField' => 'display_name',
    'disabled' => false,
])

@php
    $optionsArray = $options instanceof \Illuminate\Support\Collection ? $options->toArray() : $options;
@endphp

<div class="relative"
     wire:key="searchable-server-{{ $valueProperty }}"
     x-data="{
        open: false,
        search: '',
        valueField: '{{ $valueField }}',
        labelField: '{{ $labelField }}',
        options: {{ Js::from($optionsArray) }},
        selectedLabel: {{ Js::from($selectedLabel) }},

        selectOption(option) {
            $wire.set('{{ $valueProperty }}', option[this.valueField]);
            $wire.set('{{ $searchProperty }}', '');
            this.search = '';
            this.selectedLabel = option[this.labelField];
            this.open = false;
            $wire.call('applyFilters');
        },

        clearSelection() {
            $wire.set('{{ $valueProperty }}', null);
            $wire.set('{{ $searchProperty }}', '');
            this.search = '';
            this.selectedLabel = null;
            this.$nextTick(() => {
                this.$refs.searchInput.focus();
                this.open = true;
            });
        },

        init() {
            // Listen for cleared filters
            Livewire.on('filtersCleared', () => {
                this.selectedLabel = null;
                this.search = '';
                this.open = false;
            });

            Livewire.on('clear-instructor', () => {
                this.selectedLabel = null;
                this.search = '';
                this.open = false;
            });
        }
     }"
     x-init="
        selectedLabel = {{ Js::from($selectedLabel) }};

        // Watch for value changes from Livewire
        $watch('$wire.{{ $valueProperty }}', value => {
            if (!value) {
                selectedLabel = null;
            } else {
                const option = options.find(opt => opt[valueField] == value);
                if (option) {
                    selectedLabel = option[labelField];
                }
            }
        });

        // Watch search and update Livewire with debounce
        let searchTimeout;
        $watch('search', value => {
            clearTimeout(searchTimeout);
            if (value.length >= 2) {
                searchTimeout = setTimeout(() => {
                    $wire.set('{{ $searchProperty }}', value);
                }, 300);
            } else if (value.length === 0) {
                $wire.set('{{ $searchProperty }}', '');
            }
        });

        // Watch for options updates from Livewire
        Livewire.on('instructors-updated', (data) => {
            const instructors = data[0] || data;
            options = instructors;
        });
     "
     @click.away="open = false"
     @keydown.escape="open = false">

    <!-- Label -->
    @if($label)
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            {{ $label }}
        </label>
    @endif

    <!-- Input Container -->
    <div class="relative">
        <!-- Selected Display (shown when something is selected) -->
        <div x-show="selectedLabel"
             class="w-full flex items-center gap-2 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm dark:bg-gray-700 dark:text-white bg-white">
            <span class="flex-1 truncate" x-text="selectedLabel"></span>
            <button type="button"
                    @click="clearSelection()"
                    class="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
                    :disabled="{{ $disabled ? 'true' : 'false' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Search Input (shown when nothing is selected) -->
        <input type="text"
               x-ref="searchInput"
               x-show="!selectedLabel"
               x-model="search"
               @focus="open = true"
               @input="open = true"
               placeholder="{{ $placeholder }}"
               class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm dark:bg-gray-700 dark:text-white bg-white"
               :disabled="{{ $disabled ? 'true' : 'false' }}"
               autocomplete="off" />

        <!-- Dropdown Icon -->
        <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
            <svg class="w-4 h-4 text-gray-400 transition-transform"
                 :class="{ 'rotate-180': open }"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
    </div>

    <!-- Dropdown Results -->
    <div x-show="open && options.length > 0"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-800 shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
         x-cloak>

        <template x-for="option in options" :key="option[valueField]">
            <div @click="selectOption(option)"
                 class="cursor-pointer select-none relative py-2 pl-3 pr-9 text-gray-900 dark:text-gray-100 hover:bg-blue-50 dark:hover:bg-gray-700">
                @if(isset($optionTemplate))
                    <div x-data="{ option: option }">
                        {{ $optionTemplate }}
                    </div>
                @else
                    <span x-text="option[labelField]" class="block font-normal truncate"></span>
                @endif
            </div>
        </template>
    </div>

    <!-- Empty State -->
    <div x-show="open && options.length === 0"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-800 shadow-lg rounded-md py-3 text-base ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
         x-cloak>
        <div class="text-center text-gray-500 dark:text-gray-400 text-sm px-3">
            <span>No results found</span>
        </div>
    </div>
</div>
