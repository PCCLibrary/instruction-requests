{{-- resources/views/instruction-requests/partials/create/file-attachments.blade.php --}}
<x-card title="File Attachments" class="bg-gray-50 mb-4">
    <div class="flex flex-col">  {{-- Main container for flexbox --}}
        <div class="flex space-x-4"> {{-- Container for file inputs in a row --}}
            <div class="w-1/2 mb-4"> {{-- Adjust width as needed --}}
                <x-input-file
                    name="class_syllabus"
                    label="Class Syllabus"
                    help-text=""  {{-- Remove help text from input itself --}}
                    :multiple="true"
                    :errors="$errors->get('class_syllabus.*')"
                />
            </div>
            <div class="w-1/2"> {{-- Adjust width as needed --}}
                <x-input-file
                    name="instructor_attachments"
                    label="Additional Materials"
                    help-text="" {{-- Remove help text from input itself --}}
                    :multiple="true"
                    :errors="$errors->get('instructor_attachments.*')"
                />
            </div>
        </div>

        {{-- File Type Notice --}}
        <div class="mt-2 text-sm text-gray-500"> {{-- Notice below the inputs --}}
            <p>Accepted file types: PDF, Word documents (.doc, .docx), and text files (.txt). Maximum file size: 20MB per file</p>
        </div>
    </div>
</x-card>
