{{-- components/input-file.blade.php --}}
@props(['name', 'label', 'accept' => null, 'classes' => 'form-group', 'helptext' => null, 'required' => false])

<div class="{{ $classes }}">
    <x-label :label="$label" :required="$required" />
    <input type="file" name="{{ $name }}[]"  multiple />
    @if($helptext)
        <x-helptext name="{{ $name }}" helptext="{{ $helptext }}" />
    @endif
</div>
