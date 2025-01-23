<!-- resources/views/components/card.blade.php -->

@props(['class' => 'bg-gray-50', 'title' => '', 'headerclass' => 'text-gray-900', 'footer' => null])

<div class="border rounded-lg shadow-sm {{ $class }}">
    @if($title)
            <h5 class="px-5 mt-4 mb-0 text-2xl font-bold tracking-tight {{ $headerclass }}">{{ $title }}</h5>
    @endif

    @if(isset($body))
        {{ $body }}
    @else
        <div class="px-4 sm:p-4">
            {{ $slot }}
        </div>
    @endif

    @if(isset($footer))
        <div class="px-4 sm:p-4">
            {{ $footer }}
        </div>
    @endif
</div>
