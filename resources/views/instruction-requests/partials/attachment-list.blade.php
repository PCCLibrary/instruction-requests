{{-- resources/views/instruction-requests/admin/partials/attachment-list.blade.php --}}
<x-card title="Attachments" class="bg-gray-50">
    <x-slot name="body">
        @if ($attachments->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                    <tr class="bg-gray-50">
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            File Name
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Uploaded At
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Action
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($attachments as $attachment)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $attachment->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $attachment->created_at->format('F j, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ $attachment->getUrl() }}"
                                   class="text-cyan-700 hover:text-cyan-900"
                                   download="{{ $attachment->file_name }}"
                                   title="Download {{ $attachment->name }}">
                                   {{ $attachment->name }}
{{--                                    <x-heroicon-s-download class="h-5 w-5" />--}}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 text-sm">No attachments available.</p>
        @endif
    </x-slot>
</x-card>
