<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Monarobase\CountryList\CountryListFacade as Countries;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed; // Make sure Computed is imported
use Livewire\Attributes\Title;

new 
#[Title('Find Users')]
class extends Component {
    use WithPagination;

    #[Url(as: 'q', history: true)]
    public string $search = '';
    #[Url(history: true)]
    public ?string $nationality = null;
    #[Url(history: true)]
    public ?int $min_age = null;
    #[Url(history: true)]
    public ?int $max_age = null;

    public array $countryList = [];

    public function mount(): void
    {
        $this->countryList = Countries::getList('en', 'php');
    }

    public function updating($property): void
    {
        if (in_array($property, ['search', 'nationality', 'min_age', 'max_age'])) {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'nationality', 'min_age', 'max_age']);
        $this->resetPage();
        $this->dispatch('reset-nationality-select');
    }

    // Keep the computed property definition
    #[Computed]
    public function users(): LengthAwarePaginator
    {
        $query = User::query()
            ->with(['additionalInfo'])
            ->whereHas('additionalInfo'); // Ensure users have additional info record

        // Apply general search filter (Username ONLY)
        if (trim($this->search)) {
            $searchTerm = '%' . trim($this->search) . '%';
            $query->whereHas('additionalInfo', function ($subQuery) use ($searchTerm) {
                 // Only search the username column within the related additionalInfo table
                 $subQuery->where('username', 'like', $searchTerm);
             });            
        }

        // Apply general search filter (Firstname, Lastname, Username)
        // if (trim($this->search)) {
        //     $searchTerm = '%' . trim($this->search) . '%';
        //     $query->where(function ($q) use ($searchTerm) {
        //         $q->where('firstname', 'like', $searchTerm)
        //           ->orWhere('lastname', 'like', $searchTerm)
        //           ->orWhereHas('additionalInfo', function ($subQuery) use ($searchTerm) {
        //               $subQuery->where('username', 'like', $searchTerm);
        //           });
        //     });
        // }

        // Apply Nationality filter
        if ($this->nationality) {
            $query->whereHas('additionalInfo', function ($q) {
                $q->where('nationality', $this->nationality);
            });
        }

        // Apply Age filter
        $now = now(); // Define $now before using it
        if ($this->min_age !== null) {
            // Users must be *at least* min_age years old
            // So, their birthday must be on or before $now - min_age years
            $minBirthDate = $now->subYears($this->min_age)->endOfDay()->toDateString();
            $query->whereHas('additionalInfo', function ($q) use ($minBirthDate) {
                $q->where('birthday', '<=', $minBirthDate);
            });
             // Reset now for max_age calculation if both are set
             $now = now();
        }

        if ($this->max_age !== null) {
            // Users must be *at most* max_age years old
            // So, their birthday must be after $now - (max_age + 1) years
            $maxBirthDate = $now->subYears($this->max_age + 1)->startOfDay()->toDateString();
             $query->whereHas('additionalInfo', function ($q) use ($maxBirthDate) {
                 $q->where('birthday', '>', $maxBirthDate);
             });
        }

        // Paginate the results
        return $query->paginate(15); // Adjust items per page as needed
    }

    /**
     * Pass data to the view.
     * Explicitly pass the computed property result.
     */
    public function with(): array
    {
        return [
            'users' => $this->users, // This calls the computed property method
        ];
    }

}; ?>

<div class="py-8">
    <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100 mb-6">
        {{ __('Find Users') }}
    </h1>

    {{-- Filter Form --}}
    <div class="mb-6 p-4 bg-gray-100 dark:bg-neutral-800 rounded-lg shadow">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- General Search (Name/Username) --}}
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('Username') }}
                </label>
                <input wire:model.live.debounce.500ms="search" id="search" type="text" placeholder="Search username..."
                    class="w-full rounded-md border-gray-300 shadow-sm dark:bg-neutral-700 dark:border-neutral-600 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>

            {{-- Nationality Filter --}}
            <div wire:ignore x-data="{
                tomSelectInstance: null,
                // Use entangle for initial value and updates FROM Livewire
                selectedNationality: @entangle('nationality'),
                initTomSelect() {
                    if (typeof TomSelect === 'undefined') { console.error('TomSelect not loaded'); return; }
                    this.tomSelectInstance = new TomSelect(this.$refs.nationalitySelect, {
                        create: false,
                        valueField: 'code', // ISO code
                        labelField: 'name', // Country name
                        searchField: ['name'],
                        placeholder: '{{ __('Select Nationality...') }}',
                        options: @js(collect($countryList)->map(fn($name, $code) => ['code' => $code, 'name' => $name])->values()->all()),
                        // --- CHANGE HERE: Use $wire.set to update Livewire ---
                        onChange: (value) => {
                            // Directly update the Livewire property if the value changed
                            if ($wire.nationality !== value) {
                                 $wire.set('nationality', value);
                            }
                        },
                        // ----------------------------------------------------
                    });
            
                    // Watch for changes initiated FROM Livewire (e.g., reset)
                    this.$watch('selectedNationality', (newValue) => {
                        // Update TomSelect UI if Livewire property changes
                        if (this.tomSelectInstance.getValue() !== newValue) {
                            this.tomSelectInstance.setValue(newValue, true); // Update silently
                        }
                    });
            
                    // Set initial value for TomSelect based on Livewire property
                     if (this.selectedNationality) {
                         this.tomSelectInstance.setValue(this.selectedNationality, true);
                     }
            
                     // Listen for the reset event from Livewire
                     Livewire.on('reset-nationality-select', () => {
                         if (this.tomSelectInstance) {
                             this.tomSelectInstance.clear(); // Clear TomSelect selection
                         }
                     });
                }
            }" x-init="initTomSelect">
                <label for="nationality-select" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('Nationality') }}
                </label>
                {{-- The actual select element TomSelect will enhance --}}
                <select id="nationality-select" x-ref="nationalitySelect" placeholder="Select Nationality..."></select>
                @error('nationality') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>


            {{-- Age Filter --}}
            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <label for="min_age" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('Min Age') }}
                    </label>
                    <input wire:model.live.debounce.500ms="min_age" id="min_age" type="number" min="0"
                        placeholder="e.g., 18"
                        class="w-full rounded-md border-gray-300 shadow-sm dark:bg-neutral-700 dark:border-neutral-600 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    @error('min_age') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div class="flex-1">
                    <label for="max_age" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('Max Age') }}
                    </label>
                    <input wire:model.live.debounce.500ms="max_age" id="max_age" type="number" min="0"
                        placeholder="e.g., 99"
                        class="w-full rounded-md border-gray-300 shadow-sm dark:bg-neutral-700 dark:border-neutral-600 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    @error('max_age') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Clear Filters Button --}}
            <div class="flex items-end">
                <button wire:click="resetFilters" type="button"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-neutral-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-neutral-700 hover:bg-gray-50 dark:hover:bg-neutral-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    {{ __('Clear Filters') }}
                </button>
            </div>
        </div>
    </div>

    {{-- Results Area (Only for logged-in users) --}}
    @auth
    <div wire:loading.class="opacity-50" class="mt-8"> {{-- Show loading indicator --}}
        {{-- Use $users instead of $this->users --}}
        @if ($users->total() > 0)
        <div class="bg-white dark:bg-neutral-800 shadow sm:rounded-lg overflow-hidden">
            <ul role="list" class="divide-y divide-gray-200 dark:divide-neutral-700">
                {{-- Use $users instead of $this->users --}}
                @foreach ($users as $user)
                {{-- User Card --}}
                <x-user-card :user="$user" :show-actions="true" />
                @endforeach
            </ul>
            {{-- Pagination Links - Use $users instead of $this->users --}}
            <div
                class="px-4 py-3 sm:px-6 bg-gray-50 dark:bg-neutral-900 border-t border-gray-200 dark:border-neutral-700">
                {{ $users->links() }}
            </div>
        </div>
        @else
        {{-- Show message only if filters are applied but no results found --}}
        @if ($search || $nationality || $min_age !== null || $max_age !== null)
        <div class="text-center py-10 text-gray-500 dark:text-gray-400">
            {{ __('No users found matching your criteria.') }}
        </div>
        @else
        @endif
        @endif
    </div>
    @else
    {{-- Message for Guests --}}
    <div class="mt-8 text-center bg-blue-100 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 text-blue-700 dark:text-blue-200 px-4 py-3 rounded relative"
        role="alert">
        <strong class="font-bold">{{ __('Login Required!') }}</strong>
        <span class="block sm:inline"> {{ __('Please') }} <a href="{{ route('login') }}"
                class="font-semibold underline hover:text-blue-800 dark:hover:text-blue-100" wire:navigate>{{ __('log
                in') }}</a> {{ __('or') }} <a href="{{ route('register') }}"
                class="font-semibold underline hover:text-blue-800 dark:hover:text-blue-100" wire:navigate>{{
                __('register') }}</a> {{ __('to see the search results.') }}</span>
    </div>
    @endauth
</div>