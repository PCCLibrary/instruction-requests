{{-- resources/views/instruction-requests/admin/partials/contact-info.blade.php --}}
<x-card title="Contact Information" class="bg-gray-50">
    <x-slot name="body">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                <tr class="bg-gray-50">
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Instructor</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preferred Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pronouns</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Edit</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $instructionRequest->instructor->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $instructionRequest->instructor->display_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $instructionRequest->instructor->pronouns }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $instructionRequest->instructor->email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="{{ route('instructors.edit', $instructionRequest->instructor->id) }}"
                           class="text-cyan-700 hover:text-cyan-900"
                           target="_blank"
                           title="Click to edit the instructor information">
                            <x-heroicon-s-pencil-square class="h-5 w-5" />
                        </a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </x-slot>
</x-card>
