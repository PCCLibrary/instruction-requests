<div class="w-full rounded-md overflow-hidden border border-gray-200 dark:border-gray-700">
    {{-- Header --}}
    <div class="flex items-center justify-between bg-amber-500 dark:bg-amber-700 px-4 py-2">
        <h3 class="text-sm font-medium text-white">Assigned to Me</h3>
        @if($this->assignedRequests->count() > 0)
            <span class="text-xs text-white">{{ $this->assignedRequests->count() }} {{ Str::plural('request', $this->assignedRequests->count()) }}</span>
        @endif
    </div>

    {{-- Table --}}
    <div class="w-full overflow-x-auto">
        <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 tracking-wider">
                        Date
                    </th>
                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 tracking-wider">
                        Campus
                    </th>
                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 tracking-wider">
                        Class
                    </th>
                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 tracking-wider">
                        Instructor
                    </th>
                    <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($this->assignedRequests as $request)
                    <tr class="even:bg-gray-50 dark:even:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        {{-- Instruction Date --}}
                        <td class="px-3 py-2 text-sm text-gray-900 dark:text-gray-100">
                            @if($request->detail && $request->detail->instruction_datetime)
                                {{ \Carbon\Carbon::parse($request->detail->instruction_datetime)
                                    ->timezone('America/Los_Angeles')
                                    ->format('m/d/y g:i A') }}
                            @else
                                <span class="text-gray-400 dark:text-gray-500">Not scheduled</span>
                            @endif
                        </td>

                        {{-- Campus --}}
                        <td class="px-3 py-2 text-sm text-gray-900 dark:text-gray-100">
                            {{ $request->campus->name ?? 'N/A' }}
                        </td>

                        {{-- Class --}}
                        <td class="px-3 py-2 text-sm text-gray-900 dark:text-gray-100">
                            {{ $request->classes->course_name ?? 'N/A' }}
                        </td>

                        {{-- Instructor --}}
                        <td class="px-3 py-2 text-sm text-gray-900 dark:text-gray-100">
                            {{ $request->instructor->display_name ?? $request->instructor->name ?? 'N/A' }}
                        </td>

                        {{-- Actions --}}
                        <td class="px-3 py-2 text-right">
                            <div class="flex justify-end space-x-2">
                                {{-- Accept Button --}}
                                <form method="POST" action="{{ route('instructionRequests.accept', $request->id) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-green-600 hover:bg-green-700 rounded dark:bg-green-700 dark:hover:bg-green-600"
                                            title="Accept this request">
                                        <x-heroicon-o-check class="w-4 h-4 mr-1" />
                                        Accept
                                    </button>
                                </form>

                                {{-- Reject Button --}}
                                <form method="POST" action="{{ route('instructionRequests.reject', $request->id) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                            onclick="return confirm('Are you sure you want to reject this request? This action cannot be undone.')"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded dark:bg-red-700 dark:hover:bg-red-600"
                                            title="Reject this request">
                                        <x-heroicon-o-x-mark class="w-4 h-4 mr-1" />
                                        Reject
                                    </button>
                                </form>

                                {{-- Edit Button --}}
                                <a href="{{ route('instructionRequests.edit', $request->id) }}"
                                   class="inline-flex items-center p-2 text-xs bg-emerald-100 text-emerald-700 rounded-lg hover:bg-emerald-200 dark:bg-emerald-800 dark:text-emerald-300 dark:hover:bg-emerald-700"
                                   title="Edit this request">
                                    <x-heroicon-o-pencil-square class="w-4 h-4" />
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-3 py-4 text-sm text-center text-gray-500 dark:text-gray-400">
                            No requests assigned to you.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
