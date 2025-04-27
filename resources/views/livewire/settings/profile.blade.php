<?php

use App\Models\User;
use App\Models\UserAdditionalInfo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component {
    // public string $name = ''; // Remove name
    public string $firstname = ''; // Add firstname
    public string $lastname = ''; // Add lastname
    public string $email = '';
    public ?string $username = null;
    public ?string $birthday = null;
    public ?string $nationality = null;
    public ?string $about_me = null;

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
        // $this->name = $user->name; // Remove name
        $this->firstname = $user->firstname; // Load firstname
        $this->lastname = $user->lastname; // Load lastname
        $this->email = $user->email;

        // Load additional info if it exists
        if ($user->additionalInfo) {
            $this->username = $user->additionalInfo->username;
            $this->birthday = $user->additionalInfo->birthday?->format('Y-m-d'); // Format for input type="date"
            $this->nationality = $user->additionalInfo->nationality;
            $this->about_me = $user->additionalInfo->about_me;
        } else {
            // Sensible defaults if no additional info exists yet
            // Default username can be based on first/last name if needed, or kept simple
            $this->username = strtolower($user->firstname . $user->lastname);
        }
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            // 'name' => ['required', 'string', 'max:255'], // Remove name validation
            'firstname' => ['required', 'string', 'max:255'], // Add firstname validation
            'lastname' => ['required', 'string', 'max:255'], // Add lastname validation

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id)
            ],

            // Make username required here as well if needed, or keep nullable
            'username' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique(UserAdditionalInfo::class, 'username')->ignore($user->id, 'user_id')],
            'birthday' => ['required', 'date', 'before_or_equal:today'], // Make birthday required here too
            'nationality' => ['nullable', 'string', 'max:255'],
            'about_me' => ['nullable', 'string', 'max:65535'], // Use max text length if needed
        ]);

        // Update User model fields
        $user->firstname = $validated['firstname'];
        $user->lastname = $validated['lastname'];
        $user->email = $validated['email'];

        // Check if email was changed for verification
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Update or create UserAdditionalInfo
        UserAdditionalInfo::updateOrCreate(
            ['user_id' => $user->id], // Find by user_id
            [
                'username' => $validated['username'],
                'birthday' => $validated['birthday'],
                'nationality' => $validated['nationality'],
                'about_me' => $validated['about_me'],
            ]
        );

        $this->dispatch('profile-updated'); // Dispatch event instead of Session flash
    }

    // ... (resendVerificationNotification method remains the same) ...

}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Profile')" :subheading="__('Update your name and personal information')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            {{-- Remove Name Input --}}
            {{--
            <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name" /> --}}

            {{-- Add First Name Input --}}
            <flux:input wire:model="firstname" :label="__('First Name')" type="text" required autofocus
                autocomplete="given-name" />

            {{-- Add Last Name Input --}}
            <flux:input wire:model="lastname" :label="__('Last Name')" type="text" required
                autocomplete="family-name" />

            <div>
                <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />

                {{-- Email Verification Section (remains the same) --}}
                @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail &&!
                auth()->user()->hasVerifiedEmail())
                <div>
                    <flux:text class="mt-4">
                        {{ __('Your email address is unverified.') }}

                        <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                            {{ __('Click here to re-send the verification email.') }}
                        </flux:link>
                    </flux:text>

                    @if (session('status') === 'verification-link-sent')
                    <flux:text class="mt-2 font-medium !dark:text-green-400 !text-green-600">
                        {{ __('A new verification link has been sent to your email address.') }}
                    </flux:text>
                    @endif
                </div>
                @endif
            </div>

            {{-- Username, Birthday, Nationality, About Me (Inputs remain) --}}
            <flux:input wire:model="username" :label="__('Username')" type="text" required autocomplete="username" />
            {{-- Consider making required --}}
            <flux:input wire:model="birthday" :label="__('Birthday')" type="date" required /> {{-- Add required --}}
            <flux:input wire:model="nationality" :label="__('Nationality')" type="text" autocomplete="nationality" />
            <flux:textarea wire:model="about_me" :label="__('About Me')" rows="3"></flux:textarea>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">{{ __('Save') }}</flux:button>
                </div>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>

        <livewire:settings.delete-user-form />
    </x-settings.layout>
</section>