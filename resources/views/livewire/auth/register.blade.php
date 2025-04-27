<?php

use App\Models\User;
use App\Models\UserAdditionalInfo;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $firstname = ''; 
    public string $lastname = ''; 
    public string $username = '';
    public string $email = '';
    public ?string $birthday = null; 
    public string $password = '';
    public string $password_confirmation = '';
    public ?string $usernameCheck = null;
    public ?string $emailCheck = null;

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        // Updated validation rules
        $validated = $this->validate([
            'firstname' => ['required', 'string', 'max:255'], 
            'lastname' => ['required', 'string', 'max:255'], 
            'username' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:user_additional_infos,username'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'birthday' => ['required', 'date', 'before_or_equal:today'], 
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        // Create the User
        $validated['password'] = Hash::make($validated['password']);
        $user = User::create([
            'firstname' => $validated['firstname'], 
            'lastname' => $validated['lastname'], 
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        // Create the related additionalInfo with username and birthday
        $user->additionalInfo()->create([
            'username' => $validated['username'],
            'birthday' => $validated['birthday'], 
        ]);

        event(new Registered($user));
        Auth::login($user);

        $this->redirectIntended(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Create an account')"
        :description="__('Enter your details below to create your account')" />

    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="register" class="flex flex-col gap-6">
        <flux:input wire:model="firstname" :label="__('First Name')" type="text" required autofocus
            autocomplete="given-name" :placeholder="__('First name')" />

        <flux:input wire:model="lastname" :label="__('Last Name')" type="text" required autocomplete="family-name"
            :placeholder="__('Last name')" />

        <flux:input wire:model.debounce.500ms="username" :label="__('Username')" type="text" required
            autocomplete="username" placeholder="username123" />

        <flux:input wire:model.debounce.500ms="email" :label="__('Email address')" type="email" required
            autocomplete="email" placeholder="email@example.com" />

        <flux:input wire:model="birthday" :label="__('Birthday')" type="date" required />

        <flux:input wire:model="password" :label="__('Password')" type="password" required autocomplete="new-password"
            :placeholder="__('Password')" />

        <flux:input wire:model="password_confirmation" :label="__('Confirm password')" type="password" required
            autocomplete="new-password" :placeholder="__('Confirm password')" />

        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('Create account') }}
            </flux:button>
        </div>
    </form>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
        {{ __('Already have an account?') }}
        <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
    </div>
</div>