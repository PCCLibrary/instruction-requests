@if($materials->isNotEmpty())
    {{-- Attachments --}}

    @if($materials->isNotEmpty())
        <x-attachments :attachments="$materials" title="Materials" />
    @endif
@endif

<!-- Materials (File Upload) -->
<div class="mb-4">
    <label for="materials-dropzone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
        Materials (doc, pdf, ppt, or txt)
    </label>

    <div class="bg-blue-50 dark:bg-blue-900 rounded p-2 mb-2 text-sm text-blue-700 dark:text-blue-200">
        <div class="flex items-start">
            <svg class="h-5 w-5 text-blue-400 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p>Files will be attached to this request when you save changes. You must click "Save Changes" after uploading files.</p>
        </div>
    </div>

    <x-dropzone
        id="materials-dropzone"
        collection="materials"
        :maxFiles="2"
        :maxFileSize="20"
        tokenFieldName="upload_token"
    />
    @if($errors->has('materials.*'))
        <div class="mt-1">
            @foreach($errors->get('materials.*') as $error)
                <span class="text-red-600 text-sm">{{ $error[0] }}</span>
            @endforeach
        </div>
    @endif
</div>
