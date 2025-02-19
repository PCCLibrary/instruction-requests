{{-- components/input-date.blade.php --}}
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
    $attributes = $attributes->class([
        'w-full rounded-md focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300',
        'text-gray-800 bg-white border-gray-300 shadow-sm' => !$disabled,
        'text-gray-200 bg-gray-100 border-gray-200' => $disabled

    ])->merge([
        'type' => 'date',
        'name' => $name,
        'id' => $name,
        'value' => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : ''
    ]);

    if ($required) {
        $attributes = $attributes->merge(['required' => true]);
    }
@endphp

<div class="space-y-1 {{ $classes }}" x-data>
    <x-input-label :value="$label" :for="$name" :required="$required" />
    <input
        {{ $attributes }}
        x-bind:disabled="$store.editFormState?.isEditing === false"
    />
    @if($helptext)
        <x-helptext :name="$name" :helptext="$helptext" />
    @endif
</div>
