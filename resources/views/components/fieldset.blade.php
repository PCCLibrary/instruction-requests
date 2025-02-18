{{-- components/fieldset --}}
@props(['legend' => null, 'classes' => null, 'id' => null])

<fieldset
    @class([
        'p-2',
        $classes
    ])
    @if($id) id="{{ $id }}" @endif
>
    @if($legend)
        <legend class="text-gray-700 dark:text-gray-300 font-medium">{!! $legend !!}</legend>
    @endif
    {{ $slot }}
</fieldset>
