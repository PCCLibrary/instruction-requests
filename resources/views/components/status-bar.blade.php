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
            <div class="flex flex-col md:grid md:grid-cols-2 xl:flex xl:flex-row divide-y md:divide-y-0 xl:divide-y-0">
                @foreach($items as $index => $item)
                    <div class="flex-1 p-3 sm:p-4 border-gray-200 {{-- Consistent borders --}}
                        md:border-r md:last:border-r-0 xl:border-r xl:last:border-r-0
                        @if($index === 1 && count($items) > 1) md:border-b @endif
                    ">
                        <div class="flex items-center gap-2">
                            {{-- Icon container with matching table header colors --}}
                            <div class="flex-shrink-0 w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center {{ $item['iconBgColor'] }}">
                                <x-dynamic-component
                                    :component="'heroicon-o-'.$item['icon']"
                                    class="w-4 h-4 md:w-5 md:h-5 text-white"
                                />
                            </div>

                            {{-- Content --}}
                            <div class="min-w-0 flex-1">
                                <p class="text-xl md:text-2xl font-semibold text-gray-900">
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
