{{-- resources/views/users/partials/requests.blade.php --}}

{{-- Include the lock URL configuration --}}
@include('instruction-requests.partials.lock-urls')

<div class="mt-6">
    <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Instruction Requests for {{ $user->display_name }}</h2>
    <div class="table-responsive">
        <livewire:librarian-requests-table :librarianId="$user->id" />
    </div>
</div>

{{-- Import table-specific lock refresh script --}}
@push('scripts')
    @vite(['resources/js/table-lock-refresh.js'])
@endpush
