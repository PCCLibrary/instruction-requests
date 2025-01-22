@props(['legend' => null, 'classes' => null, 'id' => null])

<fieldset
    @class([
        'border rounded-md p-4 border-gray-200 dark:border-gray-700',
        $classes
    ])
    @if($id) id="{{ $id }}" @endif
>
    @if($legend)
        <legend class="px-2 text-gray-700 dark:text-gray-300 font-medium">{!! $legend !!}</legend>
    @endif
    {{ $slot }}
</fieldset>
