@props([
    'instructionRequests',
    'title' => 'Instruction Requests',
    'headerClasses' => 'bg-blue-500 text-white',
    'showStatus' => false
])

<div class="rounded-lg border border-gray-200 shadow-md overflow-hidden">
    {{-- Table Header --}}
    <div class="px-4 py-3 {{ $headerClasses }}">
        <h3 class="text-lg font-semibold">{{ $title }}</h3>
    </div>

    {{-- Table Body --}}
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
            <tr class="bg-gray-50">
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Instructor Name</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class Name</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Received</th>
                @if($showStatus)
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                @endif
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            @forelse($instructionRequests as $request)
                <tr class="{{ $loop->index % 2 === 0 ? 'bg-gray-50' : '' }}">
                    <td class="px-4 py-4 whitespace-nowrap">{{ $request->Instructor->name }}</td>
                    <td class="px-4 py-4 whitespace-nowrap">{{ $request->classes->course_name }}</td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($request->created_at)->timezone('America/Los_Angeles')->format('M d - g:i A') }}
                    </td>
                    @if($showStatus)
                        <td class="px-4 py-4 whitespace-nowrap">{{ $request->status }}</td>
                    @endif
                    <td class="px-4 py-4 whitespace-nowrap">
                        <a href="{{ route('instructionRequests.edit', $request->id) }}"
                           title="Click to edit this request"
                           class="text-blue-500 hover:text-blue-700"
                        >
                            <x-heroicon-o-pencil class="w-5 h-5" />
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $showStatus ? 5 : 4 }}" class="px-4 py-4 text-center text-gray-500">
                        No instruction requests found.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
