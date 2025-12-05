<div>
    {{-- Table Label --}}
    <div class="mb-2">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">My Active Requests</h2>
    </div>

    {{-- PowerGrid Table --}}
    <livewire:powergrid::powergrid :datasource="$datasource" :columns="$columns" :theme="$theme" />
</div>
