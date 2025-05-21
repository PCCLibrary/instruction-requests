@props([
    'id',
    'collection' => 'materials',
    'multiple' => true,
    'maxFiles' => 4,
    'maxFileSize' => 20,
    'acceptedFiles' => '.pdf,.doc,.docx,.ppt,.pptx,.txt,.rtf',
    'uploadUrl' => route('media.upload.public'),
    'deleteUrl' => route('media.delete.public', ['id' => '__id__']),
    'tokenUrl' => route('media.token.generate'),
    'tokenFieldName' => 'upload_token'
])

<div
    id="{{ $id }}"
    data-dropzone
    data-upload-url="{{ $uploadUrl }}"
    data-delete-url="{{ $deleteUrl }}"
    data-token-url="{{ $tokenUrl }}"
    data-token-field-id="{{ $id }}-token"
    data-token-field-name="{{ $tokenFieldName }}"
    data-max-files="{{ $maxFiles }}"
    data-max-file-size="{{ $maxFileSize }}"
    data-accepted-files="{{ $acceptedFiles }}"
    data-collection="{{ $collection }}"
    data-multiple="{{ $multiple ? 'true' : 'false' }}"
    {{ $attributes->merge(['class' => 'dropzone border-2 border-dashed border-gray-300 rounded-md p-6']) }}
>
    <div class="dz-message flex flex-col items-center justify-center text-gray-500 py-8">
        <svg class="h-12 w-12 text-gray-400 mb-2" stroke="currentColor" fill="none" viewBox="0 0 48 48">
            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4h-4m-12-4h.01M24 24h.01M16 24h.01M8 24h.01M8 16h36" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        <p class="text-lg font-medium mb-1">Drop files here or click to upload</p>
        <p class="text-sm">Maximum {{ $maxFiles }} files, {{ $maxFileSize }}MB each</p>
    </div>

    <input type="hidden" name="{{ $tokenFieldName }}" id="{{ $id }}-token" value="">
</div>

{{-- Preview Template - Hidden by default --}}
<div id="dropzone-preview-template" class="hidden">
    <div class="dz-preview dz-file-preview flex items-center mb-3 bg-gray-50 p-3 rounded">
        <div class="dz-image flex-shrink-0 mr-3">
            <svg class="h-8 w-8 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
            </svg>
        </div>
        <div class="dz-details flex-grow overflow-hidden">
            <div class="dz-filename truncate">
                <span data-dz-name class="text-sm font-medium text-gray-900"></span>
            </div>
            <div class="dz-size mt-1">
                <span data-dz-size class="text-xs text-gray-500"></span>
            </div>
            <div class="dz-progress mt-2 h-1 bg-gray-200 rounded-full overflow-hidden">
                <span class="dz-upload h-full bg-blue-600 block rounded-full" data-dz-uploadprogress style="width: 0%"></span>
            </div>
            <div class="dz-error-message mt-1 hidden">
                <span data-dz-errormessage class="text-xs font-medium text-red-600"></span>
            </div>
        </div>
        <div class="dz-success-mark text-green-600 hidden ml-2">
            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
        </div>
        <div class="dz-error-mark text-red-600 hidden ml-2">
            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </div>
        <div class="dz-remove ml-3">
            <button type="button" class="text-gray-500 hover:text-red-600" data-dz-remove>
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    </div>
</div>

@once
@push('scripts')
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<script src="{{ asset('js/components/dropzone-component.js') }}" defer></script>
@endpush

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css">
<style>
    .dropzone {
        border: 2px dashed #e2e8f0;
        border-radius: 0.375rem;
        min-height: auto;
    }
    .dropzone.dz-drag-hover {
        border-color: #4f46e5;
        background-color: #eef2ff;
    }
    .dropzone .dz-preview .dz-progress {
        height: 0.25rem;
    }
    .dropzone .dz-preview .dz-progress .dz-upload {
        background: #4f46e5;
    }
    .dropzone .dz-preview.dz-error {
        background-color: #fee2e2;
    }
</style>
@endpush
@endonce
