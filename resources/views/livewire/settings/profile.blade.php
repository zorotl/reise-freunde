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


// Remove the standalone generateDefaultUsernameHelper() function from here

new class extends Component {

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
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6" enctype="multipart/form-data">
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
            this.instance.setValue(this.value, true);
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
            {{-- --- MODIFICATION END --- --}}

            {{-- About Me --}}
            <flux:textarea wire:model="about_me" :label="__('About Me')" rows="3"></flux:textarea>

            {{-- Profile Photo Upload --}}
            <div class="col-span-6 sm:col-span-4">
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

                {{-- Add button to remove photo later if needed --}}

                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('Maximum size: 5MB. Allowed types: jpg, png, gif.') }}
                </p>
                @error('photo') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
            </div>
            {{-- End Profile Photo Upload --}}

            {{-- Form Actions --}}
            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full mt-3">{{ __('Save') }}</flux:button>
                </div>
                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>

        {{--
        <livewire:settings.delete-user-form /> --}}
    </x-settings.layout>
</section>