{{-- components/input-text.blade.php --}}
@props([
    'name',
    'label',
    'value' => '',
    'helptext' => null,
    'classes' => null,
    'required' => false,
    'disabled' => false
])

@php
    // Merge provided attributes with our base attributes
    $attributes = $attributes->class([
        'w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300',
        'bg-gray-100' => $disabled
    ])->merge([
        'type' => 'text',
        'name' => $name,
        'id' => $name,
        'value' => old($name, $value),
    ]);

    if ($helptext) {
        $attributes = $attributes->merge(['aria-describedby' => $name . '-help']);
    }

    if ($required) {
        $attributes = $attributes->merge(['required' => true]);
    }
@endphp

<div class="space-y-1 {{ $classes }}" x-data>
    @if($label)
        <x-input-label :value="$label" :for="$name" :required="$required" />
    @endif
    <input
        {{ $attributes }}
        x-bind:disabled="$store.editFormState?.isEditing === false"
    />
    @if($helptext)
        <x-helptext :name="$name" :helptext="$helptext" />
    @endif
</div>
