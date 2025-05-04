<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Actions\Logout;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.auth')] // Use a simple layout
class extends Component
{
    public $reason;
    public $expires_at;

    public function mount()
    {
        $grant = Auth::user()?->grant;
        $this->reason = $grant?->banned_reason ?? __('No reason provided.');
        $this->expires_at = $grant?->is_banned_until;
    }

    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }
}; ?>

<div class="flex flex-col gap-6 text-center">
    <flux:icon.hand-raised class="mx-auto size-10 text-red-500" /> {{-- Stop/Ban Icon --}}

    <x-auth-header :title="__('Account Suspended')" :description="__('Your account is currently suspended.')" />

    <div class="text-sm text-gray-700 dark:text-gray-300 space-y-2">
        <p><strong>{{ __('Reason:') }}</strong> {{ $reason }}</p>
        <p>
            <strong>{{ __('Suspension Ends:') }}</strong>
            @if ($expires_at)
            {{ $expires_at->format('Y-m-d H:i:s') }} ({{ $expires_at->diffForHumans() }})
            @else
            {{ __('Indefinitely') }}
            @endif
        </p>
        <p class="mt-4">{{ __('If you believe this is an error, please contact support.') }}</p>
    </div>

    <flux:button wire:click="logout" variant="outline" class="w-full">
        {{ __('Log Out') }}
    </flux:button>
</div>