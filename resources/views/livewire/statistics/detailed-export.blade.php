<div x-data="{
    filtersExpanded: @entangle('filtersExpanded'),
    showColumnModal: @entangle('showColumnModal')
}">
    <!-- Filter Panel (EXACT COPY from Trends Dashboard) -->
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
            <!-- Tabs -->
            <div class="mb-4">
                <div class="flex border-b border-gray-200 dark:border-gray-600">
                    <button
                        wire:click="$set('activeTab', 'institutional')"
                        class="flex items-center gap-2 px-4 py-2 font-medium text-sm transition-all"
                        :class="$wire.activeTab === 'institutional' ? 'text-purple-600 dark:text-purple-400 border-b-2 border-purple-600' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Institutional Periods</span>
                    </button>

                    <button
                        wire:click="$set('activeTab', 'custom')"
                        class="flex items-center gap-2 px-4 py-2 font-medium text-sm transition-all"
                        :class="$wire.activeTab === 'custom' ? 'text-purple-600 dark:text-purple-400 border-b-2 border-purple-600' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Custom Range</span>
                    </button>
                </div>

                <!-- Tab Content -->
                <div class="mt-4">
                    <!-- Institutional Periods Tab -->
                    <div x-show="$wire.activeTab === 'institutional'" x-cloak>
                        <!-- View Mode Selection -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">View By</label>
                            <div class="flex gap-2 flex-wrap">
                                <label class="flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md cursor-pointer"
                                       :class="$wire.viewMode === 'year' ? 'bg-blue-50 dark:bg-blue-900 border-blue-500' : 'hover:bg-gray-50 dark:hover:bg-gray-700'">
                                    <input type="radio" wire:model.live="viewMode" value="year" class="mr-2">
                                    <span class="text-sm dark:text-white">Academic Year</span>
                                </label>

                                <label class="flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md cursor-pointer"
                                       :class="$wire.viewMode === 'fiscal_year' ? 'bg-blue-50 dark:bg-blue-900 border-blue-500' : 'hover:bg-gray-50 dark:hover:bg-gray-700'">
                                    <input type="radio" wire:model.live="viewMode" value="fiscal_year" class="mr-2">
                                    <span class="text-sm dark:text-white">Fiscal Year</span>
                                </label>

                                <label class="flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md cursor-pointer"
                                       :class="$wire.viewMode === 'term' ? 'bg-blue-50 dark:bg-blue-900 border-blue-500' : 'hover:bg-gray-50 dark:hover:bg-gray-700'">
                                    <input type="radio" wire:model.live="viewMode" value="term" class="mr-2">
                                    <span class="text-sm dark:text-white">Term</span>
                                </label>
                            </div>
                        </div>

                        <!-- Period Selection -->
                        <div class="mb-4">
                            @if($viewMode === 'year' || $viewMode === 'fiscal_year')
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Start {{ $viewMode === 'fiscal_year' ? 'Fiscal' : 'Academic' }} Year
                                        </label>
                                        <select wire:model.live="startPeriod" wire:key="start-period-{{ $viewMode }}" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                                            @foreach($availableAcademicYears as $ay => $label)
                                                <option value="{{ $ay }}">{{ $viewMode === 'fiscal_year' ? 'FY ' : '' }}{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            End {{ $viewMode === 'fiscal_year' ? 'Fiscal' : 'Academic' }} Year
                                        </label>
                                        <select wire:model.live="endPeriod" wire:key="end-period-{{ $viewMode }}" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                                            @foreach($availableAcademicYears as $ay => $label)
                                                <option value="{{ $ay }}">{{ $viewMode === 'fiscal_year' ? 'FY ' : '' }}{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @elseif($viewMode === 'term')
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Term</label>
                                        <select wire:model.live="startPeriod" wire:key="start-period-{{ $viewMode }}" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                                            @foreach($availableTerms as $term)
                                                <option value="{{ $term->id }}">{{ $term->full_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Term</label>
                                        <select wire:model.live="endPeriod" wire:key="end-period-{{ $viewMode }}" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                                            @foreach($availableTerms as $term)
                                                <option value="{{ $term->id }}">{{ $term->full_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Custom Range Tab -->
                    <div x-show="$wire.activeTab === 'custom'" x-cloak>
                        <!-- View Mode Selection -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">View By</label>
                            <div class="flex gap-2">
                                <label class="flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md cursor-pointer"
                                       :class="$wire.viewMode === 'month' ? 'bg-blue-50 dark:bg-blue-900 border-blue-500' : 'hover:bg-gray-50 dark:hover:bg-gray-700'">
                                    <input type="radio" wire:model.live="viewMode" value="month" class="mr-2">
                                    <span class="text-sm dark:text-white">Month</span>
                                </label>

                                <label class="flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md cursor-pointer"
                                       :class="$wire.viewMode === 'day' ? 'bg-blue-50 dark:bg-blue-900 border-blue-500' : 'hover:bg-gray-50 dark:hover:bg-gray-700'">
                                    <input type="radio" wire:model.live="viewMode" value="day" class="mr-2">
                                    <span class="text-sm dark:text-white">Date Range</span>
                                </label>
                            </div>
                        </div>

                        <!-- Date Selection -->
                        <div class="mb-4">
                            @if($viewMode === 'month')
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
                    </div>
                </div>
            </div>

            <!-- Secondary Filters -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
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
                <div x-data="{ showDropdown: false }" @click.away="showDropdown = false" class="relative">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Department</label>

                    <!-- Search Input (shown when nothing is selected) -->
                    @if(!$department)
                        <input type="text"
                               wire:model.live.debounce.300ms="departmentSearch"
                               @focus="showDropdown = true"
                               @keydown.escape="showDropdown = false"
                               placeholder="Type to search departments..."
                               class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm dark:bg-gray-700 dark:text-white bg-white">

                        <!-- Dropdown Results -->
                        @if(!empty($this->filteredDepartments))
                            <div x-show="showDropdown"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-800 shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
                                 x-cloak>
                                @foreach($this->filteredDepartments as $code => $name)
                                    <div wire:click="$set('department', '{{ $code }}'); $set('departmentSearch', '')"
                                         @click="showDropdown = false"
                                         class="cursor-pointer select-none relative py-2 pl-3 pr-9 text-gray-900 dark:text-gray-100 hover:bg-blue-50 dark:hover:bg-gray-700">
                                        <p class="text-sm">{{ $name }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @elseif($departmentSearch && strlen($departmentSearch) >= 2)
                            <div x-show="showDropdown"
                                 class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-800 shadow-lg rounded-md py-3 text-base ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
                                 x-cloak>
                                <div class="text-center text-gray-500 dark:text-gray-400 text-sm px-3">
                                    No departments found
                                </div>
                            </div>
                        @endif
                    @endif

                    <!-- Selected Display (shown when something is selected) -->
                    @if($department)
                        <div class="w-full flex items-center gap-2 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm dark:bg-gray-700 dark:text-white bg-white">
                            <span class="flex-1 truncate">{{ $departments[$department] ?? $department }}</span>
                            <button type="button"
                                    wire:click="$set('department', null)"
                                    class="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Class -->
                <div x-data="{ showDropdown: false }"
                     @click.away="showDropdown = false"
                     class="relative">

                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Class
                    </label>

                    <!-- Search Input (shown when nothing is selected) -->
                    @if(!$class)
                        <input type="text"
                               wire:model.live.debounce.300ms="classSearch"
                               @focus="showDropdown = true"
                               @keydown.escape="showDropdown = false"
                               placeholder="Type to search classes..."
                               class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm dark:bg-gray-700 dark:text-white bg-white">

                        <!-- Dropdown Results -->
                        @if(!empty($this->filteredClasses) && count($this->filteredClasses) > 0)
                            <div x-show="showDropdown"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-800 shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
                                 x-cloak>
                                @foreach($this->filteredClasses as $classItem)
                                    <div wire:click="$set('class', '{{ $classItem['code'] }}'); $set('classSearch', '')"
                                         @click="showDropdown = false"
                                         class="cursor-pointer select-none relative py-2 pl-3 pr-9 text-gray-900 dark:text-gray-100 hover:bg-blue-50 dark:hover:bg-gray-700">
                                        <p class="text-sm">{{ $classItem['display'] }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @elseif($classSearch && strlen($classSearch) >= 2)
                            <div x-show="showDropdown"
                                 class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-800 shadow-lg rounded-md py-3 text-base ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
                                 x-cloak>
                                <div class="text-center text-gray-500 dark:text-gray-400 text-sm px-3">
                                    No classes found
                                </div>
                            </div>
                        @endif
                    @endif

                    <!-- Selected Display (shown when something is selected) -->
                    @if($class)
                        <div class="w-full flex items-center gap-2 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm dark:bg-gray-700 dark:text-white bg-white">
                            <span class="flex-1 truncate">{{ str_replace('-', ' ', $class) }}</span>
                            <button type="button"
                                    wire:click="$set('class', null)"
                                    class="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Instructor -->
                <div x-data="{ showDropdown: false }"
                     @click.away="showDropdown = false"
                     class="relative">

                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Instructor
                    </label>

                    <!-- Search Input (shown when nothing is selected) -->
                    @if(!$instructor)
                        <input type="text"
                               wire:model.live.debounce.300ms="instructorSearch"
                               @focus="showDropdown = true"
                               @keydown.escape="showDropdown = false"
                               placeholder="Type to search instructors..."
                               class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm dark:bg-gray-700 dark:text-white bg-white">

                        <!-- Dropdown Results -->
                        @if($this->filteredInstructors->isNotEmpty())
                            <div x-show="showDropdown"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-800 shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
                                 x-cloak>
                                @foreach($this->filteredInstructors as $inst)
                                    <div wire:click="$set('instructor', {{ $inst->id }}); $set('instructorSearch', '')"
                                         @click="showDropdown = false"
                                         class="cursor-pointer select-none relative py-2 pl-3 pr-9 text-gray-900 dark:text-gray-100 hover:bg-blue-50 dark:hover:bg-gray-700">
                                        <div>
                                            <p class="text-sm font-medium">{{ $inst->name }}</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $inst->display_name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $inst->email }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @elseif($instructorSearch && strlen($instructorSearch) >= 2)
                            <div x-show="showDropdown"
                                 class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-800 shadow-lg rounded-md py-3 text-base ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
                                 x-cloak>
                                <div class="text-center text-gray-500 dark:text-gray-400 text-sm px-3">
                                    No instructors found
                                </div>
                            </div>
                        @endif
                    @endif

                    <!-- Selected Display (shown when something is selected) -->
                    @if($instructor)
                        <div class="w-full flex items-center gap-2 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm dark:bg-gray-700 dark:text-white bg-white">
                            <span class="flex-1 truncate">{{ $instructors->firstWhere('id', $instructor)?->display_name }}</span>
                            <button type="button"
                                    wire:click="$set('instructor', null)"
                                    class="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    @endif
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

            <!-- Bottom Row: Action Buttons and Active Filters -->
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600 flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-2">
                    <button wire:click="clearFilters" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm font-medium">
                        Clear All Filters
                    </button>
                    <button @click="showColumnModal = true" class="flex items-center gap-2 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 4.5v15m6-15v15m-10.875 0h15.75c.621 0 1.125-.504 1.125-1.125V5.625c0-.621-.504-1.125-1.125-1.125H4.125C3.504 4.5 3 5.004 3 5.625v12.75c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                        <span>Customize Fields</span>
                        <span class="bg-blue-600 dark:bg-blue-700 px-2 py-0.5 rounded-full text-xs font-semibold">
                            {{ $this->selectedColumnCount }}
                        </span>
                    </button>
                </div>
                <div class="flex flex-wrap gap-2 justify-end">
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

                    @if($class)
                        <span class="inline-flex items-center gap-2 bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 px-3 py-1 rounded-full text-sm">
                            Class: {{ str_replace('-', ' ', $class) }}
                            <button wire:click="removeFilter('class')" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </span>
                    @endif

                    @if($instructor)
                        <span class="inline-flex items-center gap-2 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 px-3 py-1 rounded-full text-sm">
                            Instructor: {{ $instructors->firstWhere('id', $instructor)?->display_name }}
                            <button wire:click="removeFilter('instructor')" class="hover:text-green-600 dark:hover:text-green-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </span>
                    @endif

                    @if($assignedLibrarian)
                        <span class="inline-flex items-center gap-2 bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200 px-3 py-1 rounded-full text-sm">
                            Librarian: {{ $librarians->firstWhere('id', $assignedLibrarian)?->display_name }}
                            <button wire:click="removeFilter('assignedLibrarian')" class="hover:text-amber-600 dark:hover:text-amber-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Column Customization Modal -->
    <div x-show="showColumnModal"
         x-cloak
         @click.self="showColumnModal = false"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-5xl w-full max-h-[85vh] flex flex-col">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Customize Columns</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Hold Ctrl/Cmd to select multiple</p>
                </div>
                <button @click="showColumnModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="flex-1 overflow-y-auto p-6">
                <div class="mb-4 flex justify-end gap-2">
                    <button wire:click="selectAllColumns" class="px-3 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 dark:text-white">
                        Select All
                    </button>
                    <button wire:click="deselectAllColumns" class="px-3 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 dark:text-white">
                        Deselect All
                    </button>
                </div>

                <!-- 3-Column Layout with Checkboxes -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($tempColumnVisibility as $groupKey => $columns)
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-3 uppercase tracking-wide">
                                {{ str_replace('_', ' ', $groupKey) }}
                            </label>
                            <div class="space-y-2">
                                @foreach($columns as $index => $column)
                                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 p-2 rounded cursor-pointer">
                                        <input
                                            type="checkbox"
                                            wire:model="tempColumnVisibility.{{ $groupKey }}.{{ $index }}.visible"
                                            class="rounded border-gray-300 dark:border-gray-600 text-purple-600 focus:ring-purple-500 dark:bg-gray-700"
                                        >
                                        <span>{{ $column['label'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-3 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-2">
                <button @click="showColumnModal = false" class="px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 dark:text-white">
                    Cancel
                </button>
                <button wire:click="applyColumnVisibility" class="px-4 py-2 text-sm bg-purple-600 text-white rounded-md hover:bg-purple-700">
                    Apply
                </button>
            </div>
        </div>
    </div>

    <!-- PowerGrid Data Table -->
    <div class="mt-6">
        <livewire:statistics.detailed-export-table
            :visible-columns="$this->visibleColumns"
            wire:key="detailed-export-table-{{ $columnVisibilityKey }}" />
    </div>
</div>
