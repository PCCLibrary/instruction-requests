{{-- components/fieldset.blade.php --}}
@props(['legend' => null, 'classes' => 'mb-4', 'id' => null ])

<fieldset class="{{ $classes }}"
    @if($id)id="{{ $id }}"@endif
>
    @if($legend)<legend class="mb-4">{!! $legend !!}</legend>@endif
    {{ $slot }}
</fieldset>
