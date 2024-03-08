{{-- components/input-file.blade.php --}}
@props(['name', 'label', 'accept' => '', 'classes' => 'form-group', 'helptext' => null, 'required' => false])

<div class="{{ $classes }}">
    <x-label :label="$label" :required="$required" />
    <input type="file" name="{{ $name }}" accept="{{ $accept }}" {{ $attributes }}>
    @if($helptext)
        <x-helptext name="{{ $name }}" helptext="{{ $helptext }}" />
    @endif
</div>
