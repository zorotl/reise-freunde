<?php

use App\Models\User;
use App\Models\UserAdditionalInfo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component {
    public string $name = '';
    public string $email = '';
    public ?string $username = null;
    public ?string $birthday = null;
    public ?string $nationality = null;
    public ?string $about_me = null;
    public bool $isPrivate = false; 

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
        $this->name = $user->name;
        $this->email = $user->email;

        // Load additional info if it exists
        if ($user->additionalInfo) {
            $this->username = $user->additionalInfo->username;
            $this->birthday = $user->additionalInfo->birthday?->format('Y-m-d'); // Format for input type="date"
            $this->nationality = $user->additionalInfo->nationality;
            $this->about_me = $user->additionalInfo->about_me;
            $this->isPrivate = $user->additionalInfo->is_private ?? false;
        } else {
            // Sensible defaults if no additional info exists yet
            $this->username = $user->name; // Default username
            $this->isPrivate = false; // Default to public
        }
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id)
            ],

            'username' => ['nullable', 'string', 'max:255', Rule::unique(UserAdditionalInfo::class)->ignore($user->additionalInfo?->id, 'user_id')],
            'birthday' => ['nullable', 'date'],
            'nationality' => ['nullable', 'string', 'max:255'],
            'about_me' => ['nullable', 'string', 'max:65535'], // Use max text length if needed
            'isPrivate' => ['required', 'boolean'], // <-- Add validation for privacy setting
        ]);

        $user->fill($validated);

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
                'is_private' => $validated['isPrivate'], // <-- Save privacy setting
            ]
        );    

        // Use Livewire's flash message for better integration
        // Session::flash('status', 'verification-link-sent');
        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Profile')" :subheading="__('Update your name and personal information')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name" />

            <div>
                <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />

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

            <flux:input wire:model="username" :label="__('Username')" type="text" autocomplete="username" />
            <flux:input wire:model="birthday" :label="__('Birthday')" type="date" />
            <flux:input wire:model="nationality" :label="__('Nationality')" type="text" autocomplete="nationality" />
            <flux:textarea wire:model="about_me" :label="__('About Me')" rows="3"></flux:textarea>

            {{-- Privacy setting --}}
            <div>
                <label class="inline-flex items-center">
                    <input wire:model="isPrivate" type="checkbox"
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-neutral-800"
                        name="isPrivate">
                    <span class="ms-2 text-gray-600 dark:text-gray-300">{{ __('Private Profile') }}</span>
                </label>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('If enabled, others must request to
                    follow you.') }}</p>
                @error('isPrivate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

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