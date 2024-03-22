{{-- components/fieldset.blade.php --}}
@props(['legend' => null, 'classes' => null, 'id' => null ])

<fieldset class="mb-4 {{ $classes }}"
    @if($id)id="{{ $id }}"@endif
>
    @if($legend)<legend class="mb-4">{!! $legend !!}</legend>@endif
    {{ $slot }}
</fieldset>
