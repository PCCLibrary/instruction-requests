{{-- components/input-datetime.blade.php --}}
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
    // Process the datetime to remove seconds
    $formattedValue = '';
    if ($value) {
        $date = \Carbon\Carbon::parse($value);
        $date->setSecond(0);
        $formattedValue = $date->format('Y-m-d\TH:i');
    }

    $attributes = $attributes->class([
        'w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300',
        'bg-gray-100' => $disabled
    ])->merge([
        'type' => 'datetime-local',
        'name' => $name,
        'id' => $name,
        'value' => old($name, $formattedValue),
        'step' => '60'
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
