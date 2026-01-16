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

                <!-- Department (searchable dropdown - truncated for brevity) -->
                <div x-data="{ showDropdown: false }" @click.away="showDropdown = false" class="relative">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Department</label>
                    @if(!$department)
                        <input type="text"
                               wire:model.live.debounce.300ms="departmentSearch"
                               @focus="showDropdown = true"
                               placeholder="Type to search..."
                               class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm dark:bg-gray-700 dark:text-white bg-white">
                    @else
                        <div class="w-full flex items-center gap-2 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm dark:bg-gray-700 dark:text-white bg-white">
                            <span class="flex-1 truncate">{{ $departments[$department] ?? $department }}</span>
                            <button type="button" wire:click="$set('department', null)" class="flex-shrink-0 text-gray-400 hover:text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Class, Instructor, Librarian - similar pattern -->
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Class</label><input type="text" placeholder="Search classes..." class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm dark:bg-gray-700 dark:text-white"></div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Instructor</label><input type="text" placeholder="Search instructors..." class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm dark:bg-gray-700 dark:text-white"></div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Librarian</label><select class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm dark:bg-gray-700 dark:text-white"><option value="">All</option></select></div>
            </div>

            <!-- Bottom Row: Clear Filters + Customize Fields Button -->
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600 flex items-center justify-between">
                <button wire:click="clearFilters" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm font-medium">
                    Clear All Filters
                </button>
                <button @click="showColumnModal = true" class="flex items-center gap-2 text-red-600 hover:text-red-700 dark:text-red-500 dark:hover:text-red-400 font-medium transition-colors">
                    <span class="text-sm">customize fields</span>
                    <div class="w-6 h-6 border-2 border-current rounded-full flex items-center justify-center">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                </button>
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

                <!-- 3-Column Layout -->
                <div class="grid grid-cols-3 gap-6">
                    <!-- Column 1 -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wide">Request Information</label>
                            <select multiple size="5" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-2 py-1 text-sm dark:bg-gray-700 dark:text-white">
                                <option>ID</option>
                                <option>Status</option>
                                <option>Instruction Type</option>
                            </select>
                        </div>
                    </div>

                    <!-- Column 2 -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wide">Scheduling</label>
                            <select multiple size="4" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-2 py-1 text-sm dark:bg-gray-700 dark:text-white">
                                <option>Date/Time</option>
                                <option>Duration</option>
                            </select>
                        </div>
                    </div>

                    <!-- Column 3 -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wide">Librarian</label>
                            <select multiple size="2" class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-2 py-1 text-sm dark:bg-gray-700 dark:text-white">
                                <option>Assigned Librarian</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-3 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                <button @click="showColumnModal = false" class="px-4 py-2 text-sm bg-purple-600 text-white rounded-md hover:bg-purple-700">
                    Apply
                </button>
            </div>
        </div>
    </div>

    <!-- Data Table Placeholder -->
    <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-8 text-center">
        <p class="text-gray-600 dark:text-gray-400">PowerGrid table will be added here in next step</p>
    </div>
</div>
