{{-- components/input-file.blade.php --}}
@props( [
        'name',
        'label',
        'classes' => 'form-group',
        'helptext' => null,
        'required' => false,
        'errors' => []
        ])

<div class="{{ $classes }}">
    <x-label :label="$label" :name="$name" :required="$required" />
    <input type="file" name="{{ $name }}[]"  multiple />
    @if($helptext)
        <x-helptext name="{{ $name }}" helptext="{{ $helptext }}" />
    @endif
    @forelse ($errors as $errorArray)
        @foreach ($errorArray as $error)
            <div class="alert alert-danger">{{ $error }}</div>
        @endforeach
    @empty
        {{-- No errors to display --}}
    @endforelse
</div>
