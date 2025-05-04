<?php

use App\Models\UserAdditionalInfo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Livewire\Attributes\Title;

new 
#[Title('Settings - Privacy')]
class extends Component {
    public bool $isPrivate = false;

    public function mount(): void
    {
        $user = Auth::user();

        $this->isPrivate = $user->additionalInfo?->is_private ?? false;
    }

    public function updatePrivacy(): void
    {
        $this->validate([
            'isPrivate' => ['required', 'boolean'],
        ]);

        UserAdditionalInfo::updateOrCreate(
            ['user_id' => Auth::id()],
            ['is_private' => $this->isPrivate]
        );

        $this->dispatch('privacy-updated');
    }
};
?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Privacy Settings')" :subheading="__('Manage your profile visibility')">
        <form wire:submit="updatePrivacy" class="my-6 w-full space-y-6">

            <div>
                <label class="inline-flex items-center">
                    <input wire:model="isPrivate" type="checkbox"
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-neutral-800"
                        name="isPrivate">
                    <span class="ms-2 text-gray-600 dark:text-gray-300">{{ __('Private Profile') }}</span>
                </label>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('If enabled, others must request to follow you.') }}
                </p>
                @error('isPrivate')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit">{{ __('Save') }}</flux:button>

                <x-action-message class="me-3" on="privacy-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>

        </form>
    </x-settings.layout>
</section>