{{-- Searchable Select Component - Supports both server-side and client-side filtering --}}
@props([
    'name',
    'label',
    'placeholder' => 'Search...',
    'valueProperty',
    'searchProperty' => null,
    'options' => [],
    'mode' => 'client',
    'selectedLabel' => null,
    'valueField' => 'id',
    'labelField' => 'display_name',
    'disabled' => false,
])

@php
    $isServerMode = $mode === 'server';
    $optionsArray = $options instanceof \Illuminate\Support\Collection ? $options->toArray() : $options;
@endphp

<div class="relative"
     x-data="{
        open: false,
        search: '',
        mode: '{{ $mode }}',
        valueField: '{{ $valueField }}',
        labelField: '{{ $labelField }}',
        options: {{ Js::from($optionsArray) }},
        selectedLabel: {{ Js::from($selectedLabel) }},

        get filteredOptions() {
            if (this.mode === 'server') {
                return this.options;
            }

            if (!this.search) {
                return this.options;
            }

            const searchLower = this.search.toLowerCase();
            return this.options.filter(option => {
                const label = option[this.labelField] || '';
                return label.toLowerCase().includes(searchLower);
            });
        },

        selectOption(option) {
            @if($isServerMode)
                $wire.set('{{ $valueProperty }}', option[this.valueField]);
                $wire.set('{{ $searchProperty }}', '');
            @else
                $wire.set('{{ $valueProperty }}', option[this.valueField]);
            @endif
            this.search = '';
            this.selectedLabel = option[this.labelField];
            this.open = false;
        },

        clearSelection() {
            @if($isServerMode)
                $wire.set('{{ $valueProperty }}', null);
                $wire.set('{{ $searchProperty }}', '');
            @else
                $wire.set('{{ $valueProperty }}', null);
            @endif
            this.search = '';
            this.selectedLabel = null;
        },

        init() {
            this.$watch('options', value => {
                this.options = value;
            });
        }
     }"
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
        <!-- Selected Display / Search Input -->
        <template x-if="selectedLabel">
            <div class="w-full flex items-center gap-2 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm dark:bg-gray-700 dark:text-white bg-white">
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
        </template>

        <template x-if="!selectedLabel">
            <input type="text"
                   x-model="{{ $isServerMode ? '$wire.' . $searchProperty : 'search' }}"
                   @if($isServerMode)
                       wire:model.live.debounce.300ms="{{ $searchProperty }}"
                   @endif
                   @focus="open = true"
                   @input="open = true"
                   placeholder="{{ $placeholder }}"
                   class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm dark:bg-gray-700 dark:text-white bg-white"
                   :disabled="{{ $disabled ? 'true' : 'false' }}"
                   autocomplete="off" />
        </template>

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
    <div x-show="open && filteredOptions.length > 0"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-800 shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
         x-cloak>

        <template x-for="option in filteredOptions" :key="option[valueField]">
            <div @click="selectOption(option)"
                 class="cursor-pointer select-none relative py-2 pl-3 pr-9 text-gray-900 dark:text-gray-100 hover:bg-blue-50 dark:hover:bg-gray-700">
                <span x-text="option[labelField]" class="block font-normal truncate"></span>
            </div>
        </template>
    </div>

    <!-- Empty State -->
    <div x-show="open && filteredOptions.length === 0 && {{ $isServerMode ? 'true' : 'search' }}"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-800 shadow-lg rounded-md py-3 text-base ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
         x-cloak>
        <div class="text-center text-gray-500 dark:text-gray-400 text-sm px-3">
            <template x-if="mode === 'server' && !{{ $isServerMode ? '$wire.' . $searchProperty : 'search' }}">
                <span>Type to search...</span>
            </template>
            <template x-if="mode === 'server' && {{ $isServerMode ? '$wire.' . $searchProperty : 'search' }}">
                <span>No results found</span>
            </template>
            <template x-if="mode === 'client'">
                <span>No results found</span>
            </template>
        </div>
    </div>
</div>
