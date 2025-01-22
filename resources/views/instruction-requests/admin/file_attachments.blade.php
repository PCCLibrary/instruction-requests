@if($syllabus->isNotEmpty() || $instructorAttachments->isNotEmpty())
        {{-- Attachments --}}
        @if($syllabus->isNotEmpty())

            <x-attachments :attachments="$syllabus" title="Class syllabus" />

        @endif

        @if($instructorAttachments->isNotEmpty())

            <x-attachments :attachments="$instructorAttachments" title="Class Assignments" />

        @endif
@endif

@if($assessments->isNotEmpty() || $materials->isNotEmpty())
    {{-- Attachments --}}
    @if($materials->isNotEmpty())

        <x-attachments :attachments="$materials" title="Materials" />

    @endif

    @if($assessments->isNotEmpty())

        <x-attachments :attachments="$assessments" title="Assessments" />

    @endif
@endif

<!-- Materials (File Upload) -->
<x-input-file
    name="materials"
    label="Materials (doc, pdf, or txt)"
    :multiple="true"
    :errors="$errors->get('materials.*')"
/>


<!-- Assessments (File Upload) -->
<x-input-file
    name="assessments"
    label="Assessments (doc, pdf, or txt)"
    :multiple="true"
    :errors="$errors->get('assessments.*')"
/>
