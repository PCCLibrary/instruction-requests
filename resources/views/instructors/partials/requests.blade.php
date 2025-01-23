@props(['rows' => 5])

<div class="rounded-lg border border-gray-200 shadow-md overflow-hidden my-4">
    <div class="px-4 py-3 bg-blue-50 border-b border-blue-300 text-blue-800">
        <h3 class="text-lg font-semibold">Active Requests (placeholder!)</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
            <tr class="bg-gray-50">
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Instructor Name</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class Name</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Received</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            @for ($i = 0; $i < $rows; $i++)
                <tr class="{{ $i % 2 === 0 ? 'bg-gray-50' : '' }}">
                    <td class="px-4 py-4 whitespace-nowrap">{{ $instructor?->display_name }}</td>
                    <td class="px-4 py-4 whitespace-nowrap">Class - {{ $i + 1 }}</td>
                    <td class="px-4 py-4 whitespace-nowrap">Jan {{ 22 - $i }} - {{ 9 - $i }}:00 PM</td>
                    <td class="px-4 py-4 whitespace-nowrap">Received</td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <a href="#" title="Click to edit this request" class="text-blue-500 hover:text-blue-700">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"></path>
                            </svg>
                        </a>
                    </td>
                </tr>
            @endfor
            </tbody>
        </table>
    </div>
</div>
