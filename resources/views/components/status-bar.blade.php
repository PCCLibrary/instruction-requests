{{-- status-bar.blade.php --}}
@props([
    'items' => [],
    'containerClass' => 'mb-4'
])

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 {{ $containerClass }}">
    @foreach($items as $item)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center">
                {{-- Icon container --}}
                <div class="flex-shrink-0 {{ $item['iconBgColor'] }} rounded-lg p-3">
                    <x-dynamic-component
                        :component="'heroicon-o-'.$item['icon']"
                        class="w-6 h-6 text-white"
                    />
                </div>

                {{-- Content --}}
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                        {{ $item['infoBoxText'] }}
                    </p>
                    <p class="text-2xl font-semibold {{ $item['countColor'] ?? 'text-gray-900 dark:text-white' }}">
                        {{ $item['count'] }}
                    </p>
                </div>
            </div>
        </div>
    @endforeach
</div>
