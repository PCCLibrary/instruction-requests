{{-- components/validated-input-select.blade.php --}}
{{-- Enhanced select component with real-time validation for create form only --}}
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
    'disabled' => false,
    'validation' => null
])

@php
    $baseAttributes = $attributes->class([
        'w-full rounded-md focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300',
        'text-gray-800 bg-white border-gray-300 shadow-sm' => !$disabled,
        'text-gray-200 bg-gray-100 border-gray-200' => $disabled
    ])->merge([
        'name' => $name,
        'id' => $name
    ]);

    if ($required) {
        $baseAttributes = $baseAttributes->merge(['required' => true]);
    }
@endphp

<div class="space-y-1 {{ $classes }}" x-data="fieldValidation('{{ $name }}', {{ json_encode($validation) }})">
    @if($label)
        <x-input-label :value="$label" :for="$name" :required="$required" />
    @endif

    @if($tophelptext)
        <x-helptext :name="$name" :helptext="$tophelptext" />
    @endif

    <select
        {{ $baseAttributes }}
        x-model="fieldValue"
        @change="validateField()"
        x-bind:disabled="$store.formState?.isEditing === false"
        :class="{
            'border-red-300 dark:border-red-500 text-red-900 dark:text-red-300 focus:border-red-500 focus:ring-red-500': validationState === false
        }"
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

    <!-- Client-side validation messages (only shown when no server errors) -->
    <div x-show="validationState === false && errorMessage && !hasServerError"
         x-text="errorMessage"
         class="mt-2 text-sm text-red-600 dark:text-red-400"
         x-cloak></div>

    <!-- Server-side errors always take precedence -->
    <x-input-error class="mt-2" :messages="$errors->get($name)" />

    @if($helptext)
        <x-helptext :name="$name" :helptext="$helptext" />
    @endif
</div>
