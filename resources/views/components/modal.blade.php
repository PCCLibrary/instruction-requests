{{-- resources/views/components/modal.blade.php --}}
@props([
    'show' => false,
    'title' => '',
    'maxWidth' => 'max-w-3xl'
])

<div
    x-data="{ 
        isVisible: false,
        init() {
            // Watch the parent variable and update our local state
            this.$watch('{{ $show }}', value => { 
                this.isVisible = value;
            });
            
            // Initialize visibility based on parent state
            this.isVisible = {{ $show }};
        }
    }"
    x-show="isVisible"
    x-cloak
    @keydown.escape.window="isVisible = false; {{ $show }} = false"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    aria-modal="true"
    role="dialog"
>
    {{-- Modal Overlay --}}
    <div class="fixed inset-0 bg-gray-800 bg-opacity-50" @click="isVisible = false; {{ $show }} = false"></div>

    {{-- Modal Content --}}
    <div
        class="relative bg-white rounded-lg shadow-xl w-full {{ $maxWidth }} p-6 z-50"
        @click.away="isVisible = false; {{ $show }} = false"
    >
        @if ($title)
            <div class="mb-4 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-900">{{ $title }}</h2>
                <button
                    type="button"
                    class="text-gray-400 hover:text-gray-600"
                    @click="isVisible = false; {{ $show }} = false"
                >
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>
            </div>
        @endif

        {{ $slot }}
    </div>
</div>
