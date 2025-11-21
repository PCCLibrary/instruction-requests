<div x-data="{ filtersExpanded: @entangle('filtersExpanded') }">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Statistics Dashboard</h1>
        <p class="text-gray-600 dark:text-gray-300">Instruction request metrics and reporting.</p>
    </div>

    <!-- Filter Panel -->
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
        <!-- Filter Header -->
        <div class="bg-purple-500 dark:bg-purple-600 px-4 py-3 rounded-t-lg flex items-center justify-between">
            <h3 class="text-lg font-medium text-white">Report Filters</h3>
            <button @click="filtersExpanded = !filtersExpanded" class="text-white hover:text-gray-200">
                <svg x-show="!filtersExpanded" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
                <svg x-show="filtersExpanded" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                </svg>
            </button>
        </div>

        <!-- Filter Content -->
        <div x-show="filtersExpanded" x-collapse class="p-4">
            <!-- View Mode Selection -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    View By
                </label>
                <div class="flex gap-2">
                    <label class="flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md cursor-pointer"
                           :class="$wire.viewMode === 'year' ? 'bg-blue-50 dark:bg-blue-900 border-blue-500' : 'hover:bg-gray-50 dark:hover:bg-gray-700'">
                        <input type="radio" wire:model.live="viewMode" value="year" class="mr-2">
                        <span class="text-sm dark:text-white">Year</span>
                    </label>
                    <label class="flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md cursor-pointer"
                           :class="$wire.viewMode === 'month' ? 'bg-blue-50 dark:bg-blue-900 border-blue-500' : 'hover:bg-gray-50 dark:hover:bg-gray-700'">
                        <input type="radio" wire:model.live="viewMode" value="month" class="mr-2">
                        <span class="text-sm dark:text-white">Month</span>
                    </label>
                    <label class="flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md cursor-pointer"
                           :class="$wire.viewMode === 'day' ? 'bg-blue-50 dark:bg-blue-900 border-blue-500' : 'hover:bg-gray-50 dark:hover:bg-gray-700'">
                        <input type="radio" wire:model.live="viewMode" value="day" class="mr-2">
                        <span class="text-sm dark:text-white">Day</span>
                    </label>
                </div>
            </div>

            <!-- Date Range Selection -->
            <div class="mb-4">
                @if($viewMode === 'year')
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Fiscal Year</label>
                            <select wire:model.live="startPeriod" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                                @foreach($availableFiscalYears as $fy => $label)
                                    <option value="{{ $fy }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Fiscal Year</label>
                            <select wire:model.live="endPeriod" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                                @foreach($availableFiscalYears as $fy => $label)
                                    <option value="{{ $fy }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @elseif($viewMode === 'month')
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Month</label>
                            <input type="month" wire:model.live="startPeriod" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Month</label>
                            <input type="month" wire:model.live="endPeriod" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                            <input type="date" wire:model.live="startPeriod" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                            <input type="date" wire:model.live="endPeriod" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>
                @endif
            </div>

            <!-- Secondary Filters -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Campus -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Campus</label>
                    <select wire:model.live="campus" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                        <option value="">All Campuses</option>
                        @foreach($campuses as $campusOption)
                            <option value="{{ $campusOption->id }}">{{ $campusOption->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Department -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Department</label>
                    <select wire:model.live="department" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                        <option value="">All Departments</option>
                        @foreach($departments as $code => $name)
                            <option value="{{ $code }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Instructor -->
                <div>
                    <x-searchable-select-server
                        name="instructor"
                        label="Instructor"
                        placeholder="Type to search instructors..."
                        valueProperty="instructor"
                        searchProperty="instructorSearch"
                        :options="$this->filteredInstructors"
                        :selectedLabel="$instructor ? $instructors->firstWhere('id', $instructor)?->display_name : null"
                    >
                        <x-slot:optionTemplate>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="option.name"></p>
                                <p class="text-sm text-gray-500 dark:text-gray-400" x-text="option.display_name"></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="option.email"></p>
                            </div>
                        </x-slot:optionTemplate>
                    </x-searchable-select-server>
                </div>

                <!-- Assigned Librarian -->
                <div>
                    <x-searchable-select-client
                        name="assignedLibrarian"
                        label="Assigned Librarian"
                        placeholder="Search librarians..."
                        valueProperty="assignedLibrarian"
                        :options="$librarians"
                        :selectedLabel="$assignedLibrarian ? $librarians->firstWhere('id', $assignedLibrarian)?->display_name : null"
                    >
                        <x-slot:optionTemplate>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="option.name"></p>
                                <p class="text-sm text-gray-500 dark:text-gray-400" x-text="option.display_name"></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="option.email"></p>
                            </div>
                        </x-slot:optionTemplate>
                    </x-searchable-select-client>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-2 mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                <button wire:click="applyFilters" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                    Apply Filters
                </button>
                <button wire:click="clearFilters" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm font-medium">
                    Clear All Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Truncation Warning -->
    @if($showTruncationWarning)
        <div class="mb-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3 flex items-start gap-2">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-sm text-blue-800 dark:text-blue-200">{{ $truncationMessage }}</p>
        </div>
    @endif

    <!-- Active Filters -->
    @if($campus || $department || $instructor || $assignedLibrarian)
        <div class="mb-4 flex flex-wrap gap-2">
            @if($campus)
                <span class="inline-flex items-center gap-2 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 px-3 py-1 rounded-full text-sm">
                    Campus: {{ $campuses->find($campus)->name }}
                    <button wire:click="removeFilter('campus')" class="hover:text-blue-600 dark:hover:text-blue-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </span>
            @endif

            @if($department)
                <span class="inline-flex items-center gap-2 bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 px-3 py-1 rounded-full text-sm">
                    Department: {{ $departments[$department] }}
                    <button wire:click="removeFilter('department')" class="hover:text-purple-600 dark:hover:text-purple-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </span>
            @endif

            @if($instructor)
                <span class="inline-flex items-center gap-2 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 px-3 py-1 rounded-full text-sm">
                    Instructor: {{ $instructors->find($instructor)->display_name }}
                    <button wire:click="removeFilter('instructor')" class="hover:text-green-600 dark:hover:text-green-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </span>
            @endif

            @if($assignedLibrarian)
                <span class="inline-flex items-center gap-2 bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200 px-3 py-1 rounded-full text-sm">
                    Librarian: {{ $librarians->find($assignedLibrarian)->display_name }}
                    <button wire:click="removeFilter('assignedLibrarian')" class="hover:text-amber-600 dark:hover:text-amber-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </span>
            @endif
        </div>
    @endif

    <!-- Statistics Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Table Header -->
        <div class="bg-green-500 dark:bg-green-600 px-4 py-3 flex items-center justify-between">
            <h3 class="text-lg font-medium text-white">Instruction Type Report</h3>
            <div class="flex gap-2">
                <button wire:click="exportCsv" class="bg-white hover:bg-gray-100 text-green-700 px-3 py-1 rounded-md text-sm font-medium">
                    Export CSV
                </button>
                <button wire:click="exportExcel" class="bg-white hover:bg-gray-100 text-green-700 px-3 py-1 rounded-md text-sm font-medium">
                    Export Excel
                </button>
            </div>
        </div>

        <!-- Table Content -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider sticky left-0 bg-gray-50 dark:bg-gray-700">
                            Category
                        </th>
                        @foreach($columns as $column)
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ $column['label'] }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($statisticsData as $index => $row)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 {{ $index === 2 ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white sticky left-0 {{ $index === 2 ? 'bg-blue-50 dark:bg-blue-900/20' : 'bg-white dark:bg-gray-800' }}">
                                {{ $row['category'] }}
                            </td>
                            @foreach($columns as $column)
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-white">
                                    <span class="font-semibold {{ $index === 2 ? 'text-blue-700 dark:text-blue-400' : '' }}">
                                        {{ number_format($row[$column['key']] ?? 0) }}
                                    </span>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Table Footer -->
        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 border-t border-gray-200 dark:border-gray-600">
            <p class="text-sm text-gray-600 dark:text-gray-300">
                Showing 7 categories across {{ count($columns) }} period{{ count($columns) !== 1 ? 's' : '' }}.
                <span class="font-medium">Total sessions: {{ number_format($totalSessions) }}</span>
            </p>
        </div>
    </div>

    <!-- Summary Stats -->
    <x-status-bar
        containerClass="mt-6"
        :items="[
            [
                'iconBgColor' => 'bg-blue-500',
                'icon' => 'chart-bar',
                'infoBoxText' => 'Total Sessions',
                'count' => number_format($totalSessions)
            ],
            [
                'iconBgColor' => 'bg-green-500',
                'icon' => 'user-group',
                'infoBoxText' => 'Students Reached',
                'count' => number_format($totalStudents)
            ],
            [
                'iconBgColor' => 'bg-amber-500',
                'icon' => 'clock',
                'infoBoxText' => 'Instruction Time',
                'count' => number_format($totalInstructionHours, 1) . ' hrs'
            ],
            [
                'iconBgColor' => 'bg-indigo-500',
                'icon' => 'users',
                'infoBoxText' => 'Avg Class Size',
                'count' => number_format($averageClassSize, 1)
            ],
            [
                'iconBgColor' => 'bg-teal-500',
                'icon' => 'hand-raised',
                'infoBoxText' => 'ADA Sessions',
                'count' => number_format($adaSessions) . ' (' . number_format($adaPercentage, 1) . '%)'
            ]
        ]"
    />
</div>
