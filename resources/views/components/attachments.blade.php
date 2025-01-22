<!-- components/file-attachments.blade.php -->
@props(['attachments', 'title'])

@if($attachments->isNotEmpty())
    <ul class="space-y-2">
        <li class="font-semibold text-gray-900 dark:text-gray-100">{{ $title }}</li>
        @foreach($attachments as $item)
            <li class="text-blue-600 dark:text-blue-400">
                <span class="inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    {{ $item->file_name }} -
                    <a href="{{ $item->getUrl() }}" target="_blank" class="ml-1 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 underline">
                        View
                    </a>
                </span>
            </li>
        @endforeach
    </ul>
@else
    <div class="text-gray-700 dark:text-gray-300 font-semibold">
        No attached {{ $title }} files.
    </div>
@endif
