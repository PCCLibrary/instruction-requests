{{-- components/fieldset.blade.php --}}
@props(['legend' => null, 'classes' => 'mb-4' ])

<fieldset class="{{ $classes }}">
    @if($legend)<legend class="mb-4">{!! $legend !!}</legend>@endif

    {{ $slot }}

</fieldset>
