<!-- resources/views/components/button-group.blade.php -->

@props(['classes' => 'bg-light', 'title'=>'','headerclass' => null ])

<div class="card {{ $classes }}">
    @if($title)
        <div class="card-header {{ $headerclass }}">
            <h5 class="p-0 m-0">{{ $title }}</h5>
        </div>
    @endif
    <div class="card-body">
        {{ $slot }}
    </div>
</div>
