<?php

use App\Models\User;
use App\Models\UserAdditionalInfo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Monarobase\CountryList\CountryListFacade as Countries;
use App\Livewire\Traits\GeneratesUsername; // <-- Import the Trait
use Livewire\WithFileUploads; 
use Illuminate\Support\Facades\Storage; 
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Livewire\Attributes\Title;

// Remove the standalone generateDefaultUsernameHelper() function from here

new 
#[Title('Settings - Profile')]
class extends Component {

    use GeneratesUsername; // <-- Use the Trait
    use WithFileUploads;

    // User properties
    public string $firstname = '';
    public string $lastname = '';
    public string $email = '';

    // UserAdditionalInfo properties
    public ?string $username = null;
    public ?string $birthday = null;
    public ?string $nationality = null; // Will hold the selected ISO code
    public ?string $about_me = null;

    // Property to hold the country list for the dropdown
    public array $countryList = [];
    public array $spoken_languages = [];

    // Add property for file upload
    public $photo = null; // For temporary upload

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }
        // Load the user data
        $this->firstname = $user->firstname;
        $this->lastname = $user->lastname;
        $this->email = $user->email;

        // Load additional info if it exists
        if ($user->additionalInfo) {
            $this->username = $user->additionalInfo->username;
            $this->birthday = $user->additionalInfo->birthday?->format('Y-m-d');
            $this->nationality = $user->additionalInfo->nationality;
            $this->about_me = $user->additionalInfo->about_me;
        } else {
            // --- MODIFICATION: Call the helper method from the Trait ---
            $this->username = $this->generateDefaultUsername($user->firstname, $user->lastname);
            $this->nationality = null; // Default nationality
        }

        // Load the country list from the package
        $this->countryList = Countries::getList('en');
        $this->spoken_languages = Auth::user()?->spokenLanguages()->pluck('code')->toArray() ?? [];
    }

    /**
     * Update the profile information.
     */
     public function updateProfileInformation(): void
    {                             
        $user = Auth::user();
        if (! $user) return;

        $validCountryCodes = array_keys($this->countryList);

        // Add photo validation rules
        $validated = $this->validate([
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'lowercase', 'email', 'max:255',
                Rule::unique(User::class)->ignore($user->id)
            ],
            'username' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique(UserAdditionalInfo::class, 'username')->ignore($user->id, 'user_id')],
            'birthday' => ['required', 'date', 'before_or_equal:today'],
            'nationality' => ['nullable', 'string', 'size:2', Rule::in($validCountryCodes)],
            'about_me' => ['nullable', 'string', 'max:65535'],
            'photo' => ['nullable', 'image', 'max:5000'], // Example: Nullable, image, max 5MB
        ]);          

        // Update User model fields (same as before)
        $user->fill([
            'firstname' => $validated['firstname'],
            'lastname' => $validated['lastname'],
            'email' => $validated['email'],
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        logger('spoken_languages before sync', $this->spoken_languages);
        $user->spokenLanguages()->sync($this->spoken_languages);

        // Prepare data for UserAdditionalInfo update/create
        $additionalInfoData = [
            'username' => $validated['username'],
            'birthday' => $validated['birthday'],
            'nationality' => $validated['nationality'],
            'about_me' => $validated['about_me'],
        ];

        // Handle photo upload
        if ($this->photo) {
            // Delete old photo if it exists
            if ($user->additionalInfo?->profile_picture_path) {
                Storage::disk('public')->delete($user->additionalInfo->profile_picture_path);
            }

            // Process and store the new photo
            $manager = new ImageManager(new Driver());
            $image = $manager->read($this->photo->getRealPath());
            
            // Resize (example: fit 200x200, maintain aspect ratio, prevent upscaling)
            $image->scaleDown(width: 200, height: 200);

            // Generate a unique filename
            $filename = $this->photo->hashName();
            $directory = 'profile-pictures';
            $path = $directory . '/' . $filename;

             // Store the processed image - Intervention Image v3+
            Storage::disk('public')->put($path, (string) $image->encode()); // Encode returns image instance

            // Add the relative path to the data array
            $additionalInfoData['profile_picture_path'] = $path;        
        }

        // Update or create UserAdditionalInfo
        UserAdditionalInfo::updateOrCreate(
            ['user_id' => $user->id],
            $additionalInfoData
        );

        // Reset the temporary photo property and clear validation
        $this->reset('photo');
        $this->resetErrorBag('photo');

        // Refresh the component state (optional but good practice)
        $this->nationality = $validated['nationality']; // Keep this if needed
        $this->dispatch('profile-updated'); // Existing dispatch
        $this->dispatch('profile-picture-updated'); // Add a new event for Alpine preview reset
    }

    public function removeProfilePhoto()
    {
        $user = Auth::user();
        if (! $user) return;

        if ($user->additionalInfo?->profile_picture_path) {
            Storage::disk('public')->delete($user->additionalInfo->profile_picture_path);                    
            
            $additionalInfoData['profile_picture_path'] = null;
            UserAdditionalInfo::updateOrCreate(
                ['user_id' => $user->id],
                $additionalInfoData
            );
         
            $this->photo = null; // Reset im Livewire State
        }
    }


    /**
     * Send email verification.
     */
    public function resendVerificationNotification(): void
    {
         $user = Auth::user();
         if (! $user) return;

         if ($user->hasVerifiedEmail()) {
              $this->dispatch('notify', ['message' => 'Email already verified.', 'type' => 'info']);
             return;
         }

         $user->sendEmailVerificationNotification();

         $this->dispatch('verification-link-sent');
          $this->dispatch('notify', ['message' => 'Verification link sent!', 'type' => 'success']);
    }

}; ?>

{{-- The Blade view part remains exactly the same as before --}}
<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Profile')" :subheading="__('Update your name and personal information')">
        <form wire:submit="updateProfileInformation" x-data="{ uploading: false }"
            x-on:livewire-upload-start="uploading = true" x-on:livewire-upload-finish="uploading = false"
            x-on:livewire-upload-error="uploading = false" x-on:livewire-upload-success="uploading = false"
            class="my-6 w-full space-y-6">
            {{-- First Name, Last Name, Email, Username, Birthday Inputs (remain the same) --}}
            <flux:input wire:model="firstname" :label="__('First Name')" type="text" required autofocus
                autocomplete="given-name" />
            <flux:input wire:model="lastname" :label="__('Last Name')" type="text" required
                autocomplete="family-name" />
            <div>
                <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />
                {{-- Email Verification Section --}}
                @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !
                auth()->user()->hasVerifiedEmail())
                {{-- ... (verification section remains the same) ... --}}
                @endif
            </div>
            <flux:input wire:model="username" :label="__('Username')" type="text" required autocomplete="username" />
            <flux:input wire:model="birthday" :label="__('Birthday')" type="date" required />

            {{-- --- MODIFICATION START: Searchable Nationality Select (REVISED) --- --}}
            <div wire:ignore x-data="{
    value: @entangle('nationality'),
    instance: null,
    initSelect() {
        // Ensure TomSelect is loaded
        if (typeof TomSelect === 'undefined') {
            console.error('TomSelect not loaded');
            return;
        }
        this.instance = new TomSelect(this.$refs.nationalitySelect, {
            create: false,
            valueField: 'id', // Corresponds to the option value (ISO code)
            labelField: 'text', // Corresponds to the option text (Country Name)
            searchField: ['text'], // Allow searching by country name
            placeholder: '{{ __('Select Country...') }}',
            // Load options directly from the original select - TomSelect usually does this automatically
            // options: defined by the <option> tags below
            // Optional: Add rendering customization if needed
        });

        // When TomSelect value changes, update Livewire
        this.instance.on('change', (newValue) => {
            if (this.value !== newValue) { // Prevent triggering infinite loops
                this.value = newValue;
            }
        });

        // Watch for Livewire changes and update TomSelect
        this.$watch('value', (newValue) => {
            if (this.instance.getValue() !== newValue) {
                this.instance.setValue(newValue, true); // Update silently
            }
        });

        // Set initial value after TomSelect is initialized
        if (this.value) {
            // Wait until Livewire binding has loaded
            this.$watch('value', (newValue) => {
                if (this.instance) {
                    const current = this.instance.getValue();
                    if (JSON.stringify(current) !== JSON.stringify(newValue)) {
                        this.instance.setValue(newValue, true);
                    }
                }
            });
        }
    }
}" x-init="$nextTick(() => initSelect())">

                <label for="nationality-select" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('Nationality') }}
                </label>
                {{-- The underlying select element that TomSelect enhances --}}
                <select id="nationality-select" name="nationality" x-ref="nationalitySelect">
                    {{-- Provide options for TomSelect to pick up --}}
                    <option value="">{{ __('Select Country...') }}</option>
                    @foreach($this->countryList as $code => $name)
                    <option value="{{ $code }}" {{ $nationality==$code ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                @error('nationality') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
            
{{-- Spoken Languages Select (REVISED FIX) --}}
<div wire:ignore x-data="{
    value: @entangle('spoken_languages').live, // <-- Use .live for better sync & debounce
    instance: null,
    isInitialized: false, // Flag to track if TomSelect has been set initially
    initSelect() {
        // Ensure TomSelect is available
        if (typeof TomSelect === 'undefined') {
            console.error('TomSelect not loaded');
            return;
        }

        // Initialize TomSelect
        this.instance = new TomSelect(this.$refs.languageSelect, {
            plugins: ['remove_button'],
            placeholder: '{{ __("Select languages...") }}',
            onChange: (value) => {
                const val = Array.isArray(value) ? value : [value];

                clearTimeout(this.debounceTimeout);

                this.debounceTimeout = setTimeout(() => {
                    Alpine.store('formSync').syncing = true;
                    @this.set('spoken_languages', val).then(() => {
                        Alpine.store('formSync').syncing = false;
                    });
                }, 1000);
            },
        });

        // Watch for Livewire changes (including the initial set)
        this.$watch('value', (newValue) => {
            // Only proceed if Livewire provided an array
            if (Array.isArray(newValue)) {
                const current = this.instance.getValue();
                const currentArray = Array.isArray(current) ? current : [];

                // Check if TomSelect's value differs from Livewire's value
                if (JSON.stringify(currentArray) !== JSON.stringify(newValue)) {
                    console.log('[TomSelect] $watch updating TomSelect:', newValue);
                    this.instance.setValue(newValue, true); // Update TomSelect silently
                    this.isInitialized = true; // Mark as initialized
                }
                // If values are the same BUT we haven't run this block yet, run it.
                // This handles the very first load where value might be []
                else if (!this.isInitialized) {
                     console.log('[TomSelect] $watch initial set (even if same):', newValue);
                     this.instance.setValue(newValue, true);
                     this.isInitialized = true;
                }
            }
        });

        // Failsafe: If $watch doesn't fire quickly enough for some reason,
        // try setting the value after a short delay, but only if not already done.
        setTimeout(() => {
            if (!this.isInitialized && Array.isArray(this.value)) {
                console.log('[TomSelect] Failsafe timeout setting initial value:', this.value);
                this.instance.setValue(this.value, true);
                this.isInitialized = true;
            }
        }, 200); // Wait 200ms

    }
}" x-init="$nextTick(() => initSelect())"> {{-- Initialize when Alpine is ready --}}

    {{-- Label for the dropdown --}}
    <label for="spoken_languages" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
        {{ __('Spoken Languages') }}
    </label>

    {{-- Get current locale for language names --}}
    @php $locale = app()->getLocale(); @endphp

    {{-- The actual <select> element TomSelect will enhance --}}
    <select id="spoken_languages" name="spoken_languages[]" x-ref="languageSelect" multiple>
        {{-- Populate options from the Language model --}}
        @foreach(\App\Models\Language::all() as $language)
            <option value="{{ $language->code }}">
                {{-- Display language name in current locale, fallback to English --}}
                {{ $language->{'name_' . $locale} ?? $language->name_en }}
            </option>
        @endforeach
    </select>

    {{-- Display validation errors --}}
    @error('spoken_languages')
        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
    @enderror
</div>
{{-- End Spoken Languages Select --}}


           


            {{-- About Me --}}
            <flux:textarea wire:model="about_me" :label="__('About Me')" rows="3"></flux:textarea>

            {{-- Profile Photo Upload --}}
            {{-- <div class="col-span-6 sm:col-span-4" x-data="{ photoName: null, photoPreview: null }">
                <input type="file" id="photo" class="hidden" wire:model="photo" x-ref="photo" x-on:change="
                    photoName = $refs.photo.files[0].name;
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        photoPreview = e.target.result;
                    };
                    reader.readAsDataURL($refs.photo.files[0]);
                " />

                <label for="photo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Photo')
                    }}</label>

                <div class="mt-2" x-show="! photoPreview">
                    <img src="{{ Auth::user()->profilePictureUrl() }}" alt="{{ Auth::user()->name }}"
                        class="rounded-full h-20 w-20 object-cover">
                </div>

                <div class="mt-2" x-show="photoPreview" style="display: none;">
                    <span class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center"
                        x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                    </span>
                </div>

                <button type="button"
                    class="mt-2 me-2 inline-flex items-center px-3 py-2 border border-gray-300 dark:border-neutral-700 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-neutral-800 hover:bg-gray-50 dark:hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    x-on:click.prevent="$refs.photo.click()">
                    {{ __('Select A New Photo') }}
                </button>
                <button type="button"
                    class="mt-2 inline-flex items-center px-3 py-2 border border-red-300 dark:border-red-600 rounded-md shadow-sm text-sm font-medium text-red-600 bg-white dark:bg-neutral-800 hover:bg-red-50 dark:hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                    wire:click="removeProfilePhoto" x-show="!photoPreview">
                    {{ __('Remove Photo') }}
                </button> --}}

                {{-- Add button to remove photo later if needed --}}

                {{-- <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('Maximum size: 5MB. Allowed types: jpg, png, gif.') }}
                </p>
                @error('photo') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
            </div> --}}
            {{-- End Profile Photo Upload --}}

            {{-- Form Actions --}}
            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full mt-3" x-bind:disabled="uploading || $store.formSync.syncing">
                        <span x-show="!uploading">{{ __('Save') }}</span>
                        <span x-show="uploading">{{ __('Uploading…') }}</span>
                    </flux:button>
                </div>
                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>
    </x-settings.layout>
</section>