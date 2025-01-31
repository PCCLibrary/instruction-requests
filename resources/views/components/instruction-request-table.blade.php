@props([
    'instructionRequests',
    'title' => 'Instruction Requests',
    'showInstructor' => true,
    'showClass' => true,
    'showDate' => true,
    'showStatus' => false,
    'showCampus' => false,
    'showLibrarian' => true,
    'compact' => false,
    'limit' => null,
    'headerBgColor' => 'bg-sky-500',
    'showFooter' => false
])

<div class="w-full rounded-md overflow-hidden border border-gray-200">
    {{-- Header --}}
    @if($title)
        <div class="flex items-center justify-between {{ $headerBgColor }} px-4 py-2">
            <h3 class="text-sm font-medium text-white">{{ $title }}</h3>
            @if($limit && $instructionRequests->count() >= $limit)
                <a href="{{ route('instructionRequests.index') }}" class="text-xs text-white hover:text-blue-100">
                    View All
                </a>
            @endif
        </div>
    @endif

    {{-- Table --}}
    <div class="w-full overflow-x-auto">
        <table class="w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
            <tr>
                @if($showDate)
                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 tracking-wider">
                        {{ $showStatus ? 'Received' : 'Date' }}
                    </th>
                @endif

                @if($showCampus)
                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 tracking-wider">
                        Campus
                    </th>
                @endif

                @if($showClass)
                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 tracking-wider">
                        Class
                    </th>
                @endif

                @if($showInstructor)
                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 tracking-wider">
                        Instructor
                    </th>
                @endif

                @if($showLibrarian)
                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 tracking-wider">
                        Librarian
                    </th>
                @endif

                @if($showStatus)
                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 tracking-wider">
                        Status
                    </th>
                @endif

                <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 tracking-wider">
                    Actions
                </th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            @forelse($instructionRequests as $request)
                <tr class="even:bg-gray-50 hover:bg-gray-100 transition-colors">
                    @if($showDate)
                        <td class="px-3 py-2 {{ $compact ? 'text-sm' : 'text-base' }} text-gray-500">
                            {{ \Carbon\Carbon::parse($request->created_at)
                                ->timezone('America/Los_Angeles')
                                ->format($compact ? 'M d' : 'M d - g:i A') }}
                        </td>
                    @endif

                    @if($showCampus)
                        <td class="px-3 py-2 {{ $compact ? 'text-sm' : 'text-base' }} text-gray-900">
                            {{ $request->campus->name }}
                        </td>
                    @endif

                    @if($showClass)
                        <td class="px-3 py-2 {{ $compact ? 'text-sm' : 'text-base' }} text-gray-900">
                            <div class="flex flex-col">
                                <span>{{ $request->classes->course_name }}</span>
                            </div>
                        </td>
                    @endif

                    @if($showInstructor)
                        <td class="px-3 py-2 {{ $compact ? 'text-sm' : 'text-base' }} text-gray-900">
                            {{ $request->instructor->display_name }}
                        </td>
                    @endif

                    @if($showLibrarian)
                        <td class="px-3 py-2 {{ $compact ? 'text-sm' : 'text-base' }} text-gray-900">
                            {{ $request->detail->assignedLibrarian->display_name ?? 'Unassigned' }}
                        </td>
                    @endif

                    @if($showStatus)
                        <td class="px-3 py-2">
                                <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5
                                    {{ match($request->status) {
                                        'received' => 'bg-yellow-100 text-yellow-800',
                                        'assigned' => 'bg-blue-100 text-blue-800',
                                        'accepted' => 'bg-green-100 text-green-800',
                                        'copied' => 'bg-purple-100 text-purple-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    } }}">
                                    {{ ucfirst($request->status) }}
                                </span>
                        </td>
                    @endif

                    <td class="px-3 py-2 text-right">
                        <a href="{{ route('instructionRequests.edit', $request->id) }}"
                           class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-900">
                            <x-heroicon-o-pencil-square class="ml-1 w-4 h-4" />
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-3 py-4 text-sm text-center text-gray-500">
                        No requests found.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Footer --}}
    @if($showFooter)
        <div class="bg-white px-4 py-2 border-t border-gray-200">
            <div class="flex justify-end">
                <a href="{{ route('instructionRequests.index') }}"
                   class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium text-gray-50 bg-teal-600 border border-gray-300 rounded-md hover:bg-teal-800 transition-colors">
                    <span>View all requests</span>
                    <x-heroicon-o-arrow-right class="w-4 h-4" />
                </a>
            </div>
        </div>
    @endif
</div>
