{{-- components/validated-input-datetime.blade.php --}}
{{-- Enhanced datetime component with real-time validation for create form only --}}
@props([
    'name',
    'label',
    'value' => '',
    'helptext' => null,
    'classes' => null,
    'required' => false,
    'disabled' => false,
    'validation' => null
])

@php
    // Process the datetime to remove seconds
    $formattedValue = '';
    if ($value) {
        $date = \Carbon\Carbon::parse($value);
        $date->setSecond(0);
        $formattedValue = $date->format('Y-m-d\TH:i');
    }

    $baseAttributes = $attributes->class([
        'block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400 sm:text-sm',
        'text-gray-400 bg-gray-300 border-gray-400 cursor-not-allowed' => $disabled
    ])->merge([
        'type' => 'datetime-local',
        'name' => $name,
        'id' => $name,
        'value' => old($name, $formattedValue),
        'step' => '60'
    ]);

    if ($required) {
        $baseAttributes = $baseAttributes->merge(['required' => true]);
    }
@endphp

<div class="space-y-1 {{ $classes }}" x-data="fieldValidation('{{ $name }}', {{ json_encode($validation) }})">
    @if($label)
        <x-input-label :value="$label" :for="$name" :required="$required" />
    @endif

    <input
        {{ $baseAttributes }}
        x-model="fieldValue"
        @blur="validateField()"
        @change="validateField()"
        x-bind:disabled="$store.formState?.isEditing === false"
        x-bind:min="$store.dateValidation?.minTime"
        :class="{
            'border-red-300 dark:border-red-500 text-red-900 dark:text-red-300 focus:border-red-500 focus:ring-red-500': validationState === false
        }"
    />

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
