@props([
    'name',
    'label',
    'placeholder' => 'Search...',
    'wireModel',
    'searchProperty' => null,
    'serverSide' => false,
    'selectedValue' => null,
])

@php
    $uniqueId = 'choices-' . uniqid();
@endphp

<div class="relative">
    @if($label)
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            {{ $label }}
        </label>
    @endif

    <select
        id="{{ $uniqueId }}"
        class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-sm dark:bg-gray-700 dark:text-white"
        x-data="{
            choices: null,
            uniqueId: '{{ $uniqueId }}',
            wireModel: '{{ $wireModel }}',
            searchProperty: '{{ $searchProperty }}',
            serverSide: {{ $serverSide ? 'true' : 'false' }},
            selectedValue: @js($selectedValue),

            init() {
                this.initChoices();
            },

            initChoices() {
                const selectElement = document.getElementById(this.uniqueId);
                if (!selectElement) return;

                // Destroy existing instance if it exists
                if (this.choices) {
                    this.choices.destroy();
                }

                this.choices = new Choices(selectElement, {
                    searchEnabled: true,
                    searchPlaceholderValue: '{{ $placeholder }}',
                    itemSelectText: '',
                    shouldSort: false,
                    removeItemButton: false,
                    allowHTML: true,
                    searchResultLimit: 100,
                    @if($serverSide)
                    searchFloor: 2,
                    @endif
                    callbackOnCreateTemplates: function(template) {
                        return {
                            choice: (classNames, data) => {
                                const customProps = data.customProperties || {};
                                return template(`
                                    <div class=\"\${classNames.item} \${classNames.itemChoice} \${data.disabled ? classNames.itemDisabled : classNames.itemSelectable}\"
                                         data-select-text=\"\"
                                         data-choice
                                         \${data.disabled ? 'data-choice-disabled aria-disabled=\"true\"' : 'data-choice-selectable'}
                                         data-id=\"\${data.id}\"
                                         data-value=\"\${data.value}\"
                                         \${data.groupId > 0 ? 'role=\"treeitem\"' : 'role=\"option\"'}>
                                        <div class=\"py-1\">
                                            <p class=\"text-sm font-medium text-gray-900 dark:text-gray-100\">\${data.label}</p>
                                            \${customProps.displayName ? `<p class=\"text-sm text-gray-500 dark:text-gray-400\">\${customProps.displayName}</p>` : ''}
                                            \${customProps.email ? `<p class=\"text-xs text-gray-500 dark:text-gray-400\">\${customProps.email}</p>` : ''}
                                        </div>
                                    </div>
                                `);
                            }
                        };
                    }
                });

                @if($serverSide && $searchProperty)
                // Server-side search
                selectElement.addEventListener('search', (event) => {
                    const searchTerm = event.detail.value;
                    if (searchTerm.length < 2) return;

                    $wire.set(this.searchProperty, searchTerm);
                });
                @endif

                // Handle selection
                selectElement.addEventListener('change', (event) => {
                    const value = event.detail.value;
                    $wire.set(this.wireModel, value ? parseInt(value) : null).then(() => {
                        $wire.call('applyFilters');
                    });
                });

                // Set initial value if provided
                if (this.selectedValue) {
                    this.choices.setChoiceByValue(this.selectedValue.toString());
                }
            },

            updateChoices(options) {
                if (!this.choices) return;

                const formattedChoices = options.map(option => ({
                    value: option.id.toString(),
                    label: option.name,
                    customProperties: {
                        displayName: option.display_name,
                        email: option.email
                    }
                }));

                this.choices.clearChoices();
                this.choices.setChoices(formattedChoices, 'value', 'label', true);
            }
        }"
        x-init="init()"
        @if($serverSide && $searchProperty)
        x-on:choices-updated-{{ $wireModel }}.window="updateChoices($event.detail)"
        @endif
        @if($wireModel)
        x-on:clear-{{ $wireModel }}.window="if (choices) { choices.removeActiveItems(); choices.clearStore(); }"
        @endif
    >
        <option value="">{{ $placeholder }}</option>
    </select>
</div>
