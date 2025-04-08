{{-- components/input-select.blade.php --}}
@props([
    'name',
    'label',
    'options',
    'selected' => null,
    'showOther' => false,
    'classes' => null,
    'helptext' => null,
    'tophelptext' => null,
    'required' => false,
    'disabled' => false
])

@php
    // Merge provided attributes with our base attributes
    $attributes = $attributes->class([
        'w-full rounded-md focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300',
        'text-gray-800 bg-white border-gray-300 shadow-sm' => !$disabled,
        'text-gray-200 bg-gray-100 border-gray-200' => $disabled

    ])->merge([
        'name' => $name,
        'id' => $name
    ]);

    if ($required) {
        $attributes = $attributes->merge(['required' => true]);
    }
@endphp

<div class="space-y-1 {{ $classes }}" x-data>
    <x-input-label :value="$label" :for="$name" :required="$required" />

    @if($tophelptext)
        <x-helptext :name="$name" :helptext="$tophelptext" />
    @endif

    <select
        {{ $attributes }}
        x-bind:disabled="$store.formState?.isEditing === false"
    >
        <option value="">Select {{ $label }}</option>
        @foreach($options as $id => $display_name)
            <option value="{{ $id }}" @selected(old($name, $selected) == $id)>
                {{ $display_name }}
            </option>
        @endforeach
        @if($showOther)
            <option value="other" @selected(old($name, $selected) == 'other')>Other</option>
        @endif
    </select>

    @if($helptext)
        <x-helptext :name="$name" :helptext="$helptext" />
    @endif
</div>
