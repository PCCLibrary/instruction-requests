<!-- resources/views/components/button-group.blade.php -->

@props(['classes' => 'bg-gray-50', 'title' => '', 'headerclass' => null, 'footer' => null])

<div class="overflow-hidden bg-white border rounded-lg shadow-sm {{ $classes }}">
    @if($title)
        <div class="px-4 py-5 sm:px-6 {{ $headerclass }}">
            <h5 class="m-0 text-base font-medium leading-6 text-gray-900">{{ $title }}</h5>
        </div>
    @endif

    @if(isset($body))
        {{ $body }}
    @else
        <div class="px-4 py-5 sm:p-6">
            {{ $slot }}
        </div>
    @endif

    @if(isset($footer))
        <div class="px-4 py-4 sm:px-6 bg-gray-50">
            {{ $footer }}
        </div>
    @endif
</div>
