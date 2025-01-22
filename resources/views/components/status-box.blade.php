<div class="rounded-md p-6 shadow-md shadow-black/5 {{ $bgClass }}">
    <div class="flex items-center">
        <span class="inline-flex items-center justify-center w-12 h-12 rounded-full {{ $bgClass }} text-white mr-4">
            <i class="{{ $icon }}"></i>
        </span>
        <div>
            <div class="text-xl font-semibold text-gray-800">{{ $count }}</div>
            <div class="text-sm font-medium text-gray-500">{{ $infoBoxText }}</div>
        </div>
    </div>
</div>
