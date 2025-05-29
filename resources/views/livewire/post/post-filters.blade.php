{{-- resources/views/livewire/post-filters.blade.php --}}
<div class="mb-6 p-6 bg-white dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-2xl shadow-sm">
    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center">        
        {{ __('Filter Posts') }}
    </h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 items-end">
        {{-- Destination Country Filter --}}
        <div>
            <div class="col-span-1" wire:ignore x-data="{
                tomSelectInstance: null,
                selectedCountry: @entangle('filterDestinationCountry'),
                initTomSelect() {
                    if (typeof TomSelect === 'undefined') {
                        console.error('TomSelect is not defined. Make sure it is loaded.');
                        return;
                    }
                    this.tomSelectInstance = new TomSelect(this.$refs.destinationSelect, {
                        create: false,
                        valueField: 'code',
                        labelField: 'name',
                        searchField: ['name'],
                        placeholder: '{{ __('Any Country...') }}',
                        options: @js(collect($countryList)->map(fn($name, $code) => ['code' => $code, 'name' => $name])->values()->all()),
                        onChange: value => {
                            this.selectedCountry = value;
                            // Ensure Livewire property is updated directly
                            $wire.set('filterDestinationCountry', value);
                        }
                    });
                    this.$watch('selectedCountry', newValue => {
                        if (this.tomSelectInstance.getValue() !== newValue) {
                            this.tomSelectInstance.setValue(newValue, true);
                        }
                    });
                    if (this.selectedCountry) {
                        this.tomSelectInstance.setValue(this.selectedCountry, true);
                    }
                    Livewire.on('reset-destination-select', () => this.tomSelectInstance?.clear());
                }
            }" x-init="initTomSelect">
                <label for="destination-country-select"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('Destination Country') }}
                </label>
                <select id="destination-country-select" x-ref="destinationSelect"
                    class="tom-select-custom w-full rounded-md border-gray-300 dark:border-neutral-600 shadow-sm dark:bg-neutral-800 dark:text-gray-300 focus:border-indigo-400 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </select>
            </div>
        </div>

        {{-- Destination City Filter --}}
        <div>
            <label for="filterDestinationCity"
                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                {{ __('Destination City') }}
            </label>
            <input wire:model.live.debounce.500ms="filterDestinationCity" id="filterDestinationCity" type="text"
                placeholder="{{ __('City name...') }}"
                class="w-full rounded-md border-gray-300 dark:border-neutral-600 shadow-sm dark:bg-neutral-800 dark:text-gray-300 focus:border-indigo-400 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 px-3 py-2">
        </div>

        {{-- Travel From Date Filter --}}
        <div x-data="{
            fp: null,
            init() {
                this.fp = flatpickr(this.$refs.fromDateInput, {
                    dateFormat: 'd.m.Y',
                    allowInput: true,
                    onChange: (selectedDates, dateStr, instance) => {
                        $wire.set('filterFromDate', selectedDates[0]?.toISOString().split('T')[0]);
                    },
                });

                // Sync Livewire -> Input (when filters are reset, etc.)
                $watch('filterFromDate', (newVal) => {
                    if (newVal) {
                        this.fp.setDate(newVal);
                    }
                });
            }
        }" x-init="init">
            <label for="filterFromDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                {{ __('Travel From') }}
            </label>
            <div class="relative">
                <input x-ref="fromDateInput" type="text" placeholder="dd.mm.yyyy"
                    class="w-full rounded-md border-gray-300 dark:border-neutral-600 shadow-sm dark:bg-neutral-800 dark:text-gray-300 focus:border-indigo-400 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 pl-3 pr-10 py-2">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h.01M12 15h.01M12 11h.01M16 15h.01M16 11h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>

        {{-- Travel To Date Filter --}}
        <div x-data="{
                fp: null,
                init() {
                    this.fp = flatpickr(this.$refs.toDateInput, {
                        dateFormat: 'd.m.Y',
                        allowInput: true,
                        onChange: (selectedDates, dateStr, instance) => {
                            $wire.set('filterToDate', selectedDates[0]?.toISOString().split('T')[0]);
                        },
                    });

                    $watch('filterToDate', (newVal) => {
                        if (newVal) {
                            this.fp.setDate(newVal);
                        }
                    });
                }
            }" x-init="init">
            <label for="filterToDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                {{ __('Travel To') }}
            </label>
            <div class="relative">
                <input x-ref="toDateInput" type="text" placeholder="dd.mm.yyyy"
                    class="w-full rounded-md border-gray-300 dark:border-neutral-600 shadow-sm dark:bg-neutral-800 dark:text-gray-300 focus:border-indigo-400 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 pl-3 pr-10 py-2">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h.01M12 15h.01M12 11h.01M16 15h.01M16 11h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>




@auth
@php
    $locale = app()->getLocale();
    $languages = \App\Models\Language::orderBy('name_en')->get();
@endphp

<div class="col-span-1" wire:ignore x-data="{
    tomSelectInstance: null,
    selectedLanguage: @entangle('filterPostLanguage'),
    initTomSelect() {
        if (typeof TomSelect === 'undefined') {
            console.error('TomSelect is not defined.');
            return;
        }

        this.tomSelectInstance = new TomSelect(this.$refs.languageSelect, {
            create: false,
            placeholder: '{{ __("Filter by post language...") }}',
            onChange: (value) => {
                this.selectedLanguage = value;
                $wire.set('filterPostLanguage', value);
            }
        });

        this.$watch('selectedLanguage', (newValue) => {
            if (this.tomSelectInstance.getValue() !== newValue) {
                this.tomSelectInstance.setValue(newValue, true);
            }
        });

        if (this.selectedLanguage) {
            this.tomSelectInstance.setValue(this.selectedLanguage, true);
        }

        Livewire.on('reset-language-select', () => this.tomSelectInstance?.clear());
    }
}" x-init="initTomSelect">
    <label for="language-select-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
        {{ __('Post Language') }}
    </label>
    <select id="language-select-filter" x-ref="languageSelect"
        class="tom-select-custom w-full rounded-md border-gray-300 dark:border-neutral-600 shadow-sm dark:bg-neutral-800 dark:text-gray-300 focus:border-indigo-400 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        <option value="">{{ __('All Languages') }}</option>
        @foreach ($languages as $lang)
            <option value="{{ $lang->code }}">
                {{ $lang->{'name_' . $locale} ?? $lang->name_en }}
            </option>
        @endforeach
    </select>
</div>
@endauth





        {{-- User Nationality Filter (TomSelect) --}}
        <div>
            <div class="col-span-1" wire:ignore x-data="{
                tomSelectInstance: null,
                selectedNationality: @entangle('filterUserNationality'),
                initTomSelect() {
                    if (typeof TomSelect === 'undefined') {
                        console.error('TomSelect is not defined. Make sure it is loaded.');
                        return;
                    }
                    this.tomSelectInstance = new TomSelect(this.$refs.nationalitySelect, {
                        create: false,
                        valueField: 'code',
                        labelField: 'name',
                        searchField: ['name'],
                        placeholder: '{{ __('Any Nationality...') }}',
                        options: @js(collect($countryList)->map(fn($name, $code) => ['code' => $code, 'name' => $name])->values()->all()),
                        onChange: value => {
                            this.selectedNationality = value;
                            $wire.set('filterUserNationality', value);
                        }
                    });
                    this.$watch('selectedNationality', newValue => {
                        if (this.tomSelectInstance.getValue() !== newValue) {
                            this.tomSelectInstance.setValue(newValue, true);
                        }
                    });
                    if (this.selectedNationality) {
                        this.tomSelectInstance.setValue(this.selectedNationality, true);
                    }
                    Livewire.on('reset-nationality-select', () => this.tomSelectInstance?.clear());
                }
            }" x-init="initTomSelect">
                <label for="nationality-select-filter"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __("User's Nationality") }}
                </label>
                <select id="nationality-select-filter" x-ref="nationalitySelect"
                    class="tom-select-custom w-full rounded-md border-gray-300 dark:border-neutral-600 shadow-sm dark:bg-neutral-800 dark:text-gray-300 focus:border-indigo-400 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </select>
            </div>
        </div>        

        {{-- User Age Filter --}}
        <div class="flex items-end gap-4 col-span-1 sm:col-span-2 lg:col-span-2">
            <div class="flex-1">
                <label for="filterMinAge" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('Min Age') }}
                </label>
                <input wire:model.live.debounce.500ms="filterMinAge" id="filterMinAge" type="number" min="0"
                    placeholder="e.g., 18"
                    class="w-full rounded-md border-gray-300 dark:border-neutral-600 shadow-sm dark:bg-neutral-800 dark:text-gray-300 focus:border-indigo-400 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 px-3 py-2">
            </div>
            <div class="flex-1">
                <label for="filterMaxAge" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('Max Age') }}
                </label>
                <input wire:model.live.debounce.500ms="filterMaxAge" id="filterMaxAge" type="number" min="0"
                    placeholder="e.g., 99"
                    class="w-full rounded-md border-gray-300 dark:border-neutral-600 shadow-sm dark:bg-neutral-800 dark:text-gray-300 focus:border-indigo-400 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 px-3 py-2">
            </div>
        </div>

        {{-- Clear Filters Button --}}
        <div class="col-span-full lg:col-span-1 flex items-end justify-end">
            <button wire:click="resetFilters" type="button"
                class="w-full sm:w-auto px-4 py-2 border border-gray-300 dark:border-neutral-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-neutral-600 hover:bg-gray-100 dark:hover:bg-neutral-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                {{ __('Clear Filters') }}
            </button>
        </div>
    </div>
</div>