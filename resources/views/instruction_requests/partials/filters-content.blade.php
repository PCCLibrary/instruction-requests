{{-- resources/views/instruction-requests/partials/filters-content.blade.php --}}
<div class="filter-wrapper">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 p-4">
        {{-- Date Range Filter --}}
        <div>
            <label for="date-range" class="block text-sm font-medium text-gray-700 mb-1">
                Date Range
            </label>
            <select wire:model.live="dateRange"
                    id="date-range"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="">All Time</option>
                @foreach($dateRanges as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        {{-- Campus Filter --}}
        <div>
            <label for="campus-filter" class="block text-sm font-medium text-gray-700 mb-1">
                Campus
            </label>
            <select wire:model.live="selectedCampus"
                    id="campus-filter"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="">All Campuses</option>
                @foreach($campuses as $campus)
                    <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Status Filter --}}
        <div>
            <label for="status-filter" class="block text-sm font-medium text-gray-700 mb-1">
                Status
            </label>
            <select wire:model.live="selectedStatus"
                    id="status-filter"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="">All Statuses</option>
                @foreach($statuses as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        {{-- Instruction Type Filter --}}
        <div>
            <label for="type-filter" class="block text-sm font-medium text-gray-700 mb-1">
                Instruction Type
            </label>
            <select wire:model.live="selectedType"
                    id="type-filter"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="">All Types</option>
                @foreach($types as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Custom Date Range (shown only when custom is selected) --}}
    <div x-data="{ show: @entangle('dateRange').live === 'custom' }"
         x-show="show"
         class="grid grid-cols-1 md:grid-cols-2 gap-4 px-4 pb-4">
        <div>
            <label for="start-date" class="block text-sm font-medium text-gray-700 mb-1">
                Start Date
            </label>
            <input type="date"
                   wire:model.live="startDate"
                   id="start-date"
                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        </div>
        <div>
            <label for="end-date" class="block text-sm font-medium text-gray-700 mb-1">
                End Date
            </label>
            <input type="date"
                   wire:model.live="endDate"
                   id="end-date"
                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        </div>
    </div>

    {{-- Filter Actions --}}
    <div class="flex justify-end space-x-3 px-4 pb-4">
        <button wire:click="resetFilters"
                type="button"
                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Reset Filters
        </button>
        <button wire:click="applyFilters"
                type="button"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Apply Filters
        </button>
    </div>
</div>
