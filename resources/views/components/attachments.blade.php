<!-- components/file-attachments.blade.php -->
@props(['attachments', 'title'])

@if($attachments->isNotEmpty())
    <ul class="space-y-2 mb-4">
        <li class="text-lg font-semibold leading-6 text-gray-900">{{ $title }}</li>
        @foreach($attachments as $item)
            <li class="flex items-center gap-2 text-gray-900">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                </svg>
                <span class="text-sm">{{ $item->file_name }}</span>
                <span class="text-sm text-gray-500">-</span>
                <a href="{{ $item->getUrl() }}"
                   target="_blank"
                   class="text-sm text-blue-600 hover:text-blue-800">
                    View
                </a>
            </li>
        @endforeach
    </ul>
@else
    <div class="text-sm text-gray-600">
        No attached {{ strtolower($title) }} files.
    </div>
@endif
