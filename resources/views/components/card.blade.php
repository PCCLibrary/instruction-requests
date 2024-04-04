<!-- resources/views/components/button-group.blade.php -->

@props(['classes' => 'bg-light', 'title'=>'','headerclass' => null, 'footer' => null ])

<div class="card {{ $classes }}">
    @if($title)
        <div class="card-header {{ $headerclass }}">
            <h5 class="p-0 m-0">{{ $title }}</h5>
        </div>
    @endif
        @if(isset($body))
            {{ $body }}
        @else
        <div class="card-body">
        {{ $slot }}
        </div>
        @endif

    @if(isset($footer))
        <div class="card-footer bg-light text-dark">
            {{ $footer }}
        </div>
    @endif
</div>
