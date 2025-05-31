@props([
    'id',
    'label' => '',
    'entangle' => '',
    'options' => [],
])

@php
    $optionsJson = Js::from($options);
@endphp

<div
    wire:ignore
    x-data="{
        selectedValues: @entangle($entangle).defer,
        tomSelectInstance: null,
        optionsData: {{ $optionsJson }},
        
        init() {
            if (this.$refs.select.tomselect) {
                this.tomSelectInstance = this.$refs.select.tomselect; // rebind
                return;
            }

            this.tomSelectInstance = new TomSelect(this.$refs.select, {
                plugins: ['remove_button'],
                maxItems: null,
                placeholder: '{{ __("Select...") }}',
                create: false,
                options: this.optionsData,
                searchField: 'label',
                onChange: (value) => {
                    this.selectedValues = value;   
                    $wire.set('{{ $entangle }}', value);                
                },
                render: {
                    item: (data, escape) => {
                        return `<div>${escape(data.label)}</div>`;
                    },
                    option: (data, escape) => {
                        return `<div>${escape(data.label)}</div>`;
                    },
                },
            });

            this.tomSelectInstance.setValue(this.selectedValues || []);

            this.$watch('selectedValues', (value) => {
                if (this.tomSelectInstance) {
                    this.tomSelectInstance.setValue(value || []);
                }
            });

            Livewire.on('reset-user-filter-selects', () => {
                if (this.tomSelectInstance) {
                    this.tomSelectInstance.clear();
                }
            });
        }
    }"
>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
        {{ $label }}
    </label>

    <select
        x-ref="select"
        id=$id
        multiple
        class="tom-select-custom w-full rounded-md border-gray-300 dark:border-neutral-600 shadow-sm dark:bg-neutral-800 dark:text-gray-300"
    ></select>
</div>