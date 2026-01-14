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

        <!-- Term editor will go here in next commit -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-8">
            <p class="text-gray-600">Term editor coming in next commit...</p>
        </div>
    </div>
</div>
