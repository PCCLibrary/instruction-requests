@if($materials->isNotEmpty())
    {{-- Attachments --}}

    @if($materials->isNotEmpty())
        <x-attachments :attachments="$materials" title="Materials" />
    @endif
@endif

<!-- Materials (File Upload) -->
<x-input-file
    name="materials"
    class="mb-4"
    label="Materials (doc, pdf, or txt)"
    :multiple="true"
    :errors="$errors->get('materials.*')"
/>
