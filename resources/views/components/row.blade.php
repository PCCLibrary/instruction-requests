{{-- components/fieldset.blade.php --}}
@props([ 'classes' => 'mb-4', 'id' => null ])

<div class="row {{  $classes }}"
@if($id)id="{{ $id }}"@endif>
    {{ $slot }}
</div>
