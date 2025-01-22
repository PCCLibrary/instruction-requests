{{-- components/helptext.blade.php --}}
@props(['name' => '', 'helptext' => ''])

<p id="{{ $name }}-help" class="mt-1 text-sm text-gray-500 dark:text-gray-400 mb-2">
    {!! $helptext !!}
</p>
