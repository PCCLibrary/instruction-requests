{{-- components/fieldset.blade.php --}}
@props([ 'classes' => 'mb-4' ])

<div class="row {{  $classes }}">

    {{ $slot }}

</div>
