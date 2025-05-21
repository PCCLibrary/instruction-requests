{{-- resources/views/instruction-requests/partials/create/file-attachments.blade.php --}}
<x-card title="File Attachments" class="bg-gray-50 mb-4 on-campus-fields remote-fields asynchronous-fields">
    <div class="flex flex-col">  {{-- Main container for flexbox --}}
        <div class="bg-blue-50 dark:bg-blue-900 rounded p-2 mb-3 text-sm text-blue-700 dark:text-blue-200">
            <div class="flex items-start">
                <svg class="h-5 w-5 text-blue-400 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p>Files will be attached to this request when you save the form. You must complete and submit the entire form to attach files.</p>
            </div>
        </div>

        <div class="mb-4">
            <label for="materials-dropzone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Materials (doc, pdf, ppt, or txt)
            </label>
            <x-dropzone
                id="materials-dropzone"
                collection="materials"
                :maxFiles="4"
                :maxFileSize="20"
                tokenFieldName="upload_token"
                class="mb-2"
            />
        </div>

        {{-- File Type Notice --}}
        <div class="mt-2 text-sm text-gray-500"> {{-- Notice below the inputs --}}
            <p>Accepted file types: PDF, Word documents (.doc, .docx), PowerPoint (.ppt, .pptx), and text files (.txt, .rtf). Maximum file size: 20MB per file</p>
        </div>
    </div>
</x-card>
