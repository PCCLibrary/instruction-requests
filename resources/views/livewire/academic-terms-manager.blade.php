<div class="flex min-h-screen bg-gray-100">
    <!-- Sidebar -->
    <div class="w-64 bg-white border-r border-gray-200">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-xs font-semibold text-gray-500 uppercase">Academic Years</h2>
        </div>

        <div class="p-2 space-y-1">
            @foreach($years as $year)
                <button
                    wire:click="selectYear('{{ $year }}')"
                    class="w-full text-left px-4 py-2 rounded-md {{ $selectedYear === $year ? 'bg-blue-50 border border-blue-500' : 'hover:bg-gray-50' }}">
                    <div class="text-sm font-semibold">{{ $year }}</div>
                    @if($year === $currentYear)
                        <div class="text-xs text-emerald-600 font-medium">Current</div>
                        <div class="text-xs text-gray-400">Protected</div>
                    @endif
                </button>
            @endforeach
        </div>

        <div class="absolute bottom-0 w-64 p-4 bg-gray-50 border-t border-gray-200">
            <button class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium">
                Create New Year
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Academic Year {{ $selectedYear }}</h1>
            <p class="text-sm text-gray-600">Define term dates for instruction scheduling and reporting</p>
        </div>

        <!-- Term Editor -->
        <div class="bg-white rounded-lg shadow border border-gray-200">
            <div class="bg-purple-500 px-4 py-3 rounded-t-lg">
                <h3 class="text-lg font-medium text-white">Term Dates</h3>
            </div>

            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Term</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Start Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">End Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Weeks</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <!-- Fall -->
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-6 rounded bg-orange-500"></div>
                                <span class="font-medium">Fall</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <input type="date" wire:model.live="fallStart" class="border border-gray-300 rounded px-2 py-1 text-sm">
                        </td>
                        <td class="px-6 py-4">
                            <input type="date" wire:model.live="fallEnd" class="border border-gray-300 rounded px-2 py-1 text-sm">
                        </td>
                        <td class="px-6 py-4 text-right font-semibold">
                            {{ $this->calculateWeeks($fallStart, $fallEnd) }}
                        </td>
                    </tr>

                    <!-- Winter -->
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-6 rounded bg-blue-500"></div>
                                <span class="font-medium">Winter</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <input type="date" wire:model.live="winterStart" class="border border-gray-300 rounded px-2 py-1 text-sm">
                        </td>
                        <td class="px-6 py-4">
                            <input type="date" wire:model.live="winterEnd" class="border border-gray-300 rounded px-2 py-1 text-sm">
                        </td>
                        <td class="px-6 py-4 text-right font-semibold">
                            {{ $this->calculateWeeks($this->winterStart, $winterEnd) }}
                        </td>
                    </tr>

                    <!-- Spring -->
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-6 rounded bg-emerald-500"></div>
                                <span class="font-medium">Spring</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <input type="date" wire:model.live="springStart" class="border border-gray-300 rounded px-2 py-1 text-sm">
                        </td>
                        <td class="px-6 py-4">
                            <input type="date" wire:model.live="springEnd" class="border border-gray-300 rounded px-2 py-1 text-sm">
                        </td>
                        <td class="px-6 py-4 text-right font-semibold">
                            {{ $this->calculateWeeks($this->springStart, $springEnd) }}
                        </td>
                    </tr>

                    <!-- Summer -->
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-6 rounded bg-amber-500"></div>
                                <span class="font-medium">Summer</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <input type="date" wire:model.live="summerStart" class="border border-gray-300 rounded px-2 py-1 text-sm">
                        </td>
                        <td class="px-6 py-4">
                            <input type="date" wire:model.live="summerEnd" class="border border-gray-300 rounded px-2 py-1 text-sm">
                        </td>
                        <td class="px-6 py-4 text-right font-semibold">
                            {{ $this->calculateWeeks($this->summerStart, $summerEnd) }}
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="px-6 py-3 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-xs font-medium text-gray-600">Ensure dates don't overlap between terms</span>
                </div>
                <span class="text-xs text-gray-500">Total: <strong>{{ $this->totalWeeks }}</strong> weeks</span>
            </div>
        </div>

        <!-- Buttons -->
        <div class="mt-6 flex justify-between items-center">
            <p class="text-xs text-gray-500">Changes affect instruction scheduling and statistics</p>
            <div class="flex gap-3">
                <button wire:click="cancel" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium hover:bg-gray-50">
                    Cancel
                </button>
                <button wire:click="save" class="px-6 py-2 bg-purple-500 text-white rounded-md text-sm font-medium hover:bg-purple-600">
                    Save Changes
                </button>
            </div>
        </div>
    </div>
</div>
