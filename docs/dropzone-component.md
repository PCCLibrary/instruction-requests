# Dropzone Component Documentation

## Overview

The Dropzone component provides a modern, user-friendly file upload interface for the Library Instruction System. It uses the [Dropzone.js](https://www.dropzonejs.com/) library with a token-based security approach for temporary file storage and Spatie MediaLibrary integration.

## Features

- Drag-and-drop file upload interface
- Client-side file validation (file type, size)
- Upload progress indicators
- Token-based security for unauthenticated uploads
- Automatic file type detection and icon display
- Error handling with user-friendly messages
- Alpine.js compatibility for dynamic forms

## Usage

### Basic Usage

```blade
<x-dropzone
    id="materials-dropzone"
    collection="materials"
    :maxFiles="4"
    :maxFileSize="20"
    tokenFieldName="upload_token"
/>
```

### With Custom Label

```blade
<div class="mb-4">
    <label for="syllabus-dropzone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
        Class Syllabus
    </label>
    <x-dropzone
        id="syllabus-dropzone"
        collection="syllabus"
        :maxFiles="2"
        :maxFileSize="20"
        tokenFieldName="upload_token"
    />
    @if($errors->has('syllabus.*'))
        <div class="mt-1">
            @foreach($errors->get('syllabus.*') as $error)
                <span class="text-red-600 text-sm">{{ $error[0] }}</span>
            @endforeach
        </div>
    @endif
</div>
```

## Configuration Options

| Option | Description | Default |
|--------|-------------|---------|
| `id` | Unique identifier for the dropzone (required) | - |
| `collection` | Media collection name for Spatie MediaLibrary | "materials" |
| `multiple` | Allow multiple file uploads | true |
| `maxFiles` | Maximum number of files allowed | 4 |
| `maxFileSize` | Maximum file size in MB | 20 |
| `acceptedFiles` | Comma-separated list of allowed MIME types | ".pdf,.doc,.docx,.ppt,.pptx,.txt,.rtf" |
| `uploadUrl` | URL for file uploads | route('media.upload.public') |
| `deleteUrl` | URL for file deletion | route('media.delete.public', ['id' => '__id__']) |
| `tokenUrl` | URL for token generation | route('media.token.generate') |
| `tokenFieldName` | Name of the hidden form field for the token | "upload_token" |

## Integration Details

### Form Integration

The component automatically adds a hidden input field with the upload token, which is submitted with the form:

```html
<input type="hidden" name="upload_token" id="materials-dropzone-token" value="generated-token-here">
```

Make sure your form has:
- `enctype="multipart/form-data"` attribute
- CSRF token (`@csrf` directive)

### Backend Integration

The Dropzone component works with the existing MediaController routes:

- `/api/token/generate` - Generates a secure upload token
- `/api/media/upload` - Handles file uploads with token validation
- `/api/media/delete/{id}` - Handles file deletion

### Alpine.js Compatibility

The component is designed to work seamlessly with Alpine.js-powered forms, including:
- Visibility toggling based on form state
- Dynamic form fields
- Conditional validation

## Example Implementation

### Create Form

```blade
<x-card title="File Attachments" class="bg-gray-50 mb-4">
    <div class="flex flex-col">
        <div class="flex space-x-4">
            <div class="w-1/2 mb-4">
                <label for="syllabus-dropzone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Class Syllabus
                </label>
                <x-dropzone
                    id="syllabus-dropzone"
                    collection="syllabus"
                    :maxFiles="2"
                    :maxFileSize="20"
                    tokenFieldName="upload_token"
                    class="mb-2"
                />
            </div>
            <div class="w-1/2">
                <label for="materials-dropzone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Additional Materials
                </label>
                <x-dropzone
                    id="materials-dropzone"
                    collection="instructor_attachments"
                    :maxFiles="2"
                    :maxFileSize="20"
                    tokenFieldName="upload_token"
                    class="mb-2"
                />
            </div>
        </div>

        <div class="mt-2 text-sm text-gray-500">
            <p>Accepted file types: PDF, Word documents (.doc, .docx), PowerPoint (.ppt, .pptx), and text files (.txt, .rtf). Maximum file size: 20MB per file</p>
        </div>
    </div>
</x-card>
```

### Edit Form

```blade
@if($materials->isNotEmpty())
    <x-attachments :attachments="$materials" title="Materials" />
@endif

<div class="mb-4">
    <label for="materials-dropzone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
        Materials (doc, pdf, ppt, or txt)
    </label>
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
```

## Troubleshooting

### File Upload Fails

- Check browser console for JavaScript errors
- Verify CSRF token is present in the page
- Ensure token generation endpoint is accessible
- Check file size and type against server restrictions

### Visual Issues

- Make sure Dropzone.js and CSS are properly loaded
- Check for CSS conflicts with other components
- Verify previewTemplate element exists in the page

### Alpine.js Conflicts

- Check for JavaScript errors in browser console
- Ensure Dropzone initialization doesn't interfere with Alpine.js
- Verify x-data attributes don't conflict with Dropzone elements

## Technical Implementation Notes

The component uses:
- Token-based security for unauthenticated uploads
- IIFE pattern for JavaScript isolation
- MutationObserver for Alpine.js compatibility
- Proper error handling with user feedback
- Automated token generation and form field population
