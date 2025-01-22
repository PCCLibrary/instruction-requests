{{-- components/fieldset.blade.php --}}
@props(['legend' => null, 'classes' => null, 'id' => null ])

<fieldset class="{{ $classes }}"
    @if($id)id="{{ $id }}"@endif
>
    @if($legend)<legend>{!! $legend !!}</legend>@endif
    {{ $slot }}
</fieldset>
