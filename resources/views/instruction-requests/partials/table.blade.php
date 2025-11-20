{{-- resources/views/instruction-requests/partials/table.blade.php --}}

{{-- Include the lock URL configuration --}}
@include('instruction-requests.partials.lock-urls')

<div class="table-responsive">
    <livewire:instruction-request-table />
</div>

{{-- Import table-specific lock refresh script --}}
@push('scripts')
    @vite(['resources/js/table-lock-refresh.js'])
@endpush
