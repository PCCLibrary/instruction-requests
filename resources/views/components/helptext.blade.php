{{-- components/helptext.blade.php --}}
@props(['name' => '', 'helptext' => '' ])

<p id="{{ $name }}-help" class="form-text text-muted mb-2">
    {!! $helptext !!}
</p>
