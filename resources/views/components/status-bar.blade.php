{{-- status-bar.blade.php --}}
@props([
    'items' => [],
    'containerClass' => 'mb-6',
    'headerBgColor' => 'bg-sky-500'
])

<div class="{{ $containerClass }}">
    <div class="w-full rounded-md overflow-hidden border border-gray-200">
        {{-- Header --}}
        <div class="flex items-center {{ $headerBgColor }} px-4 py-2">
            <h3 class="text-sm font-medium text-white">Status</h3>
        </div>

        {{-- Status Items --}}
        <div class="bg-white">
            <div class="flex divide-x divide-gray-200">
                @foreach($items as $item)
                    <div class="flex-1 p-4">
                        <div class="flex items-center gap-3">
                            {{-- Icon container with matching table header colors --}}
                            <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center {{ $item['iconBgColor'] }}">
                                <x-dynamic-component
                                    :component="'heroicon-o-'.$item['icon']"
                                    class="w-5 h-5 text-white"
                                />
                            </div>

                            {{-- Content --}}
                            <div class="min-w-0 flex-1">
                                <p class="text-2xl font-semibold text-gray-900">
                                    {{ $item['count'] }}
                                </p>
                                <p class="text-sm text-gray-500 truncate">
                                    {{ $item['infoBoxText'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
