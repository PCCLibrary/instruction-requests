<!-- resources/views/components/button-group.blade.php -->

@props(['classes' => 'bg-light', 'title'=>''])

<div class="card">
    <div class="card-header {{ $classes }}">
        <h5 class="p-0 m-0">{{ $title }}</h5>
    </div>
    <div class="card-body">
        {{ $slot }}
    </div>
</div>
