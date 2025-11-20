{{-- resources/views/instruction-requests/partials/table.blade.php --}}

{{-- Include the lock URL configuration --}}
@include('instruction-requests.partials.lock-urls')

<div class="table-responsive">
    <livewire:instruction-request-table>
        <x-slot:header>
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-600 dark:text-gray-300">
                    Export {{ $this->exportableRecordCount() }} records:
                </span>
                <button wire:click="exportXls"
                        class="px-3 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:bg-green-700 dark:hover:bg-green-600">
                    XLS
                </button>
                <button wire:click="exportCsv"
                        class="px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-700 dark:hover:bg-blue-600">
                    CSV
                </button>
            </div>
        </x-slot:header>
    </livewire:instruction-request-table>
</div>

{{-- Import table-specific lock refresh script --}}
@push('scripts')
    @vite(['resources/js/table-lock-refresh.js'])
@endpush
