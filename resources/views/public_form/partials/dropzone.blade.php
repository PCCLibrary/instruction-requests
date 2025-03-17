{{-- resources/views/public_form/partials/dropzone.blade.php --}}
@props([
    'name' => 'materials',
    'label' => 'Upload Files',
    'classes' => 'form-group',
    'helptext' => 'Drag and drop files here, or click to browse. Supports PDF, Word, PowerPoint, and text files (max 20MB per file).',
    'required' => false,
    'errors' => []
])

<div class="{{ $classes }}">
    @include('public_form.partials.label', [
        'label' => $label,
        'name' => $name,
        'required' => $required
    ])
    
    <div class="dropzone-container">
        <div id="dropzone-upload" class="file-dropzone">
            <div class="dz-message">
                <div class="text-center">
                    <i class="fa fa-cloud-upload fa-3x text-muted mb-2"></i>
                    <h4>Drop files here or click to upload</h4>
                    <p class="text-muted">
                        Accepted file types: PDF, Word, PowerPoint, and text files<br>
                        (Maximum file size: 20MB per file, up to 4 files total)
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <div id="dropzone-preview-template" class="d-none">
        <div class="dz-preview dz-file-preview">
            <div class="d-flex align-items-center p-2 border rounded mb-2">
                <div class="dz-image me-3">
                    <i class="fa fa-file fa-2x text-muted"></i>
                </div>
                <div class="dz-details flex-grow-1">
                    <div class="dz-filename">
                        <span data-dz-name></span> (<span class="dz-size" data-dz-size></span>)
                    </div>
                    <div class="progress mt-1" style="height: 5px;">
                        <div class="progress-bar bg-success" role="progressbar" data-dz-uploadprogress></div>
                    </div>
                </div>
                <div class="dz-actions ms-2">
                    <button type="button" class="btn btn-sm btn-danger" data-dz-remove>
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="dz-error-message text-danger small mt-1" data-dz-errormessage></div>
        </div>
    </div>
    
    <div id="dropzone-uploaded-files" class="mt-2"></div>
    
    <input type="hidden" name="upload_token" id="upload-token" value="">
    
    @if($helptext)
        @include('public_form.partials.helptext', [
            'name' => $name,
            'helptext' => $helptext
        ])
    @endif
    
    @forelse ($errors as $errorArray)
        @foreach ($errorArray as $error)
            <div class="alert alert-danger">{{ $error }}</div>
        @endforeach
    @empty
        {{-- No errors to display --}}
    @endforelse
</div>

<style>
    .file-dropzone {
        border: 2px dashed #ccc;
        border-radius: 4px;
        background-color: #f8f9fa;
        min-height: 150px;
        padding: 20px;
        cursor: pointer;
        transition: border-color 0.3s ease;
    }

    .file-dropzone:hover {
        border-color: #007bff;
    }

    .file-dropzone .dz-message {
        margin: 1em 0;
    }

    .dz-progress {
        display: block;
        width: 100%;
        height: 5px;
        margin-top: 5px;
    }

    .dz-preview {
        margin-bottom: 10px;
    }

    .dz-remove {
        color: #dc3545;
        cursor: pointer;
        text-decoration: none;
    }

    .dz-remove:hover {
        color: #bd2130;
    }

    .dz-upload {
        display: block;
        background-color: #28a745;
        width: 0;
        height: 100%;
    }

    .dz-error-message {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
</style>
