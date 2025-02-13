{{-- components/label.blade.php --}}
@props([
    'value',
    'for' => null,
    'required' => false,
    'class' => '' // Provide a default empty string for the class
])

<label for="{{ $for }}" {{ $attributes->merge(['class' => 'block font-medium text-sm text-gray-700 dark:text-gray-300 ' . $class]) }}>
    {{ $value ?? $slot }}
    @if($required)
        <span class="text-red-500">*</span>
    @endif
</label>
