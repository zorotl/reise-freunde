<div class="mb-6 p-6 bg-white dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-2xl shadow-sm">
    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">
        {{ __('Filter Users') }}
    </h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 items-end">
        {{-- Nationality --}}
        <div wire:ignore x-data="{
            selected: @entangle('filterUserNationality'),
            init() {
                const select = this.$refs.nationalitySelect;
                const instance = new TomSelect(select, {
                    create: false,
                    placeholder: '{{ __("Any Nationality...") }}',
                    onChange: val => $wire.set('filterUserNationality', val),
                });

                this.$watch('selected', value => {
                    if (instance.getValue() !== value) {
                        instance.setValue(value, true);
                    }
                });

                if (this.selected) {
                    instance.setValue(this.selected, true);
                }

                Livewire.on('reset-user-filter-selects', () => instance.clear());
            }
        }" x-init="init">
            <label for="nationality-select" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                {{ __("User's Nationality") }}
            </label>
            <select id="nationality-select" x-ref="nationalitySelect"
                class="tom-select-custom w-full rounded-md border-gray-300 dark:border-neutral-600 shadow-sm dark:bg-neutral-800 dark:text-gray-300">
                <option value="">{{ __('Any Nationality') }}</option>
                @foreach ($countryList as $code => $name)
                    <option value="{{ $code }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Gender --}}
        <div wire:ignore x-data="{
            selected: @entangle('filterGender'),
            init() {
                const select = this.$refs.genderSelect;
                const instance = new TomSelect(select, {
                    create: false,
                    placeholder: '{{ __("Any Gender...") }}',
                    onChange: val => $wire.set('filterGender', val),
                });

                this.$watch('selected', value => {
                    if (instance.getValue() !== value) {
                        instance.setValue(value, true);
                    }
                });

                if (this.selected) {
                    instance.setValue(this.selected, true);
                }

                Livewire.on('reset-user-filter-selects', () => instance.clear());
            }
        }" x-init="init">
            <label for="gender-select" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                {{ __('Gender') }}
            </label>
            <select id="gender-select" x-ref="genderSelect"
                class="tom-select-custom w-full rounded-md border-gray-300 dark:border-neutral-600 shadow-sm dark:bg-neutral-800 dark:text-gray-300">
                <option value="">{{ __('Any Gender') }}</option>
                <option value="female">{{ __('Female') }}</option>
                <option value="male">{{ __('Male') }}</option>
                <option value="diverse">{{ __('Diverse') }}</option>
            </select>
        </div>

        {{-- Min/Max Age --}}
        <div class="flex gap-4">
            {{-- Min Age --}}
            <div class="flex-1">
                <label for="filterMinAge" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('Min Age') }}
                </label>
                <input
                    id="filterMinAge"
                    type="number"
                    min="0"
                    step="1"
                    inputmode="numeric"
                    wire:model.lazy="filterMinAge"
                    class="w-full rounded-md border-gray-300 dark:border-neutral-600 shadow-sm dark:bg-neutral-800 dark:text-gray-300 px-3 py-2"
                    placeholder="e.g. 18"
                />
            </div>
            {{-- Max Age --}}
            <div class="flex-1">
                <label for="filterMaxAge" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('Max Age') }}
                </label>
                <input
                    id="filterMaxAge"
                    type="number"
                    min="0"
                    step="1"
                    inputmode="numeric"
                    wire:model.lazy="filterMaxAge"
                    class="w-full rounded-md border-gray-300 dark:border-neutral-600 shadow-sm dark:bg-neutral-800 dark:text-gray-300 px-3 py-2"
                    placeholder="e.g. 99"
                />
            </div>             
        </div>

        {{-- Spoken Languages  --}}
        <x-multi-select
            id="spoken-language-select"
            label="{{ __('Spoken Languages') }}"
            ref="LanguagesSelect"
            :entangle="'filterLanguages'"
            :options="$allLanguages->map(fn($l) => ['value' => $l->code, 'label' => $l->name_en])->toArray()"
        />

        {{-- Travel Styles --}}
        <x-multi-select
            id="travel-style-select"
            label="{{ __('Travel Styles') }}"
            ref="travelStylesSelect"
            :entangle="'filterTravelStyles'"
            :options="$allTravelStyles->map(fn($s) => ['value' => $s->id, 'label' => $s->name])->toArray()"
        />

        {{-- Hobbies --}}
        <x-multi-select
            id="hobby-select"
            label="{{ __('Hobbies') }}"
            ref="hobbySelect"
            :entangle="'filterHobbies'"
            :options="$allHobbies->map(fn($h) => ['value' => $h->id, 'label' => $h->name])->toArray()"
        /> 

        {{-- Verified Users (by social media or id) --}}
        <div>
            <label for="filter_verified" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                {{ __('Show users with verified identity') }}
            </label>
            <input type="checkbox" id="filter_verified" wire:model.live="filterVerified"
                class="rounded border-gray-300 dark:border-neutral-600 text-indigo-600 focus:ring-indigo-500" />
        </div>

        {{-- Trusted Users (real world confirmation) --}}
        <div>
            <label for="filter_trusted" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                {{ __('Show users trusted by others') }}
            </label>
            <input type="checkbox" id="filter_trusted" wire:model.live="filterTrusted"
                class="rounded border-gray-300 dark:border-neutral-600 text-indigo-600 focus:ring-indigo-500" />
        </div>

        {{-- Clear Filters --}}
        <div class="col-span-full flex justify-end">
            <button wire:click="resetFilters" type="button"
                class="px-4 py-2 border border-gray-300 dark:border-neutral-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-neutral-600 hover:bg-gray-100 dark:hover:bg-neutral-500 focus:ring-indigo-500">
                {{ __('Clear Filters') }}
            </button>
        </div>       
    </div>
</div>
