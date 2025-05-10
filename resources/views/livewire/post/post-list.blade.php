{{-- resources/views/livewire/post/post-list.blade.php --}}
<div>
    {{-- Page Header --}}
    <div class="mb-5 flex justify-between items-center flex-wrap gap-4">
        <div>
            @if ($show === 'all')
            <h2 class="text-xl font-semibold text-gray-900 dark:text-stone-400">Find your travel buddy</h2>
            @elseif ($show === 'my')
            <h2 class="text-xl font-semibold text-gray-900 dark:text-stone-400">My Posts</h2>
            @endif
        </div>
        @auth {{-- Only show Create button if logged in --}}
        <a wire:navigate href="{{ route('post.create') }}"
            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
            Create New Entry
        </a>
        @endauth
    </div>

    {{-- Search and Filter Section --}}
    @unless ($show === 'my') {{-- Don't show filters on 'My Posts' page --}}
    <livewire:search />
    <div
        class="mb-6 p-6 bg-white dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-2xl shadow-sm">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">
            {{ __('Filter Posts') }}
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Destination Country Filter --}}
            <div>
                <div class="col-span-1" wire:ignore x-data="{
                    tomSelectInstance: null,
                    selectedCountry: @entangle('filterDestinationCountry'),
                    initTomSelect() {
                        if (typeof TomSelect === 'undefined') return;
                        this.tomSelectInstance = new TomSelect(this.$refs.destinationSelect, {
                            create: false,
                            valueField: 'code',
                            labelField: 'name',
                            searchField: ['name'],
                            placeholder: '{{ __('Any Country...') }}',
                            options: @js(collect($countryList)->map(fn($name, $code) => ['code' => $code, 'name' => $name])->values()->all()),
                            onChange: value => $wire.set('filterDestinationCountry', value)
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
                        placeholder="Any Country..."></select>
                </div>

            </div>

            {{-- Destination City Filter --}}
            <div>
                <label for="filterDestinationCity"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('Destination City') }}
                </label>
                <input wire:model.live.debounce.500ms="filterDestinationCity" id="filterDestinationCity" type="text"
                    placeholder="City name..."
                    class="w-full rounded-md border-gray-300 dark:border-neutral-600 shadow-sm dark:bg-neutral-700 dark:text-gray-300 focus:border-indigo-400 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>

            {{-- Travel From --}}
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
            }" x-init="init" x-data="{ filterFromDate: @entangle('filterFromDate') }">
                <label for="filterFromDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('Travel From') }}
                </label>
                <input x-ref="fromDateInput" type="text" placeholder="dd.mm.yyyy"
                    class="w-full rounded-md border-gray-300 dark:border-neutral-600 shadow-sm dark:bg-neutral-700 dark:text-gray-300 focus:border-indigo-400 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>

            {{-- Travel To --}}
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
            }" x-init="init" x-data="{ filterToDate: @entangle('filterToDate') }">
                <label for="filterToDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('Travel To') }}
                </label>
                <input x-ref="toDateInput" type="text" placeholder="dd.mm.yyyy"
                    class="w-full rounded-md border-gray-300 dark:border-neutral-600 shadow-sm dark:bg-neutral-700 dark:text-gray-300 focus:border-indigo-400 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>

            {{-- User Nationality Filter (TomSelect) --}}
            <div class="col-span-1" wire:ignore x-data="{
                tomSelectInstance: null,
                selectedNationality: @entangle('filterUserNationality'),
                initTomSelect() {
                    if (typeof TomSelect === 'undefined') return;
                    this.tomSelectInstance = new TomSelect(this.$refs.nationalitySelect, {
                        create: false,
                        valueField: 'code',
                        labelField: 'name',
                        searchField: ['name'],
                        placeholder: '{{ __('Any Nationality...') }}',
                        options: @js(collect($countryList)->map(fn($name, $code) => ['code' => $code, 'name' => $name])->values()->all()),
                        onChange: value => $wire.set('filterUserNationality', value)
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
                    placeholder="Any Nationality..."></select>
            </div>

            {{-- User Age Filter --}}
            <div class="flex items-end gap-4 col-span-1 md:col-span-2 lg:col-span-1">
                <div class="flex-1">
                    <label for="filterMinAge" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('Min Age') }}
                    </label>
                    <input wire:model.live.debounce.500ms="filterMinAge" id="filterMinAge" type="number" min="0"
                        placeholder="e.g., 18"
                        class="w-full rounded-md border-gray-300 dark:border-neutral-600 shadow-sm dark:bg-neutral-700 dark:text-gray-300 focus:border-indigo-400 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div class="flex-1">
                    <label for="filterMaxAge" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('Max Age') }}
                    </label>
                    <input wire:model.live.debounce.500ms="filterMaxAge" id="filterMaxAge" type="number" min="0"
                        placeholder="e.g., 99"
                        class="w-full rounded-md border-gray-300 dark:border-neutral-600 shadow-sm dark:bg-neutral-700 dark:text-gray-300 focus:border-indigo-400 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
            </div>

            {{-- Clear Filters Button --}}
            <div class="col-span-full lg:col-span-1 flex items-end justify-end">
                <button wire:click="resetFilters" type="button"
                    class="px-4 py-2 border border-gray-300 dark:border-neutral-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-neutral-600 hover:bg-gray-100 dark:hover:bg-neutral-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    {{ __('Clear Filters') }}
                </button>
            </div>
        </div>
    </div>
    @endunless

    {{-- Results Area --}}
    <section class="bg-white dark:bg-neutral-800 shadow sm:rounded-lg p-6">
        {{-- <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-200 mb-4">{{ __('Posts') }}</h2> --}}
        <div class="space-y-4">
            @if ($entries && $entries->count() > 0)
            @foreach ($entries as $post)
            {{-- Pass the individual post object to the PostCardSection component --}}
            <livewire:parts.post-card-section :post="$post" :show="$show" wire:key="post-card-{{ $post->id }}" />
            @endforeach
            {{-- Pagination Links --}}
            <div class="mt-4">
                {{ $entries->links() }}
            </div>
            @else
            <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-8">
                {{ __('No posts found matching your criteria.') }}
            </p>
            @endif
        </div>
    </section>
</div>