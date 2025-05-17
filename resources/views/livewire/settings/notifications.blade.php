<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;

new 
#[Title('Settings - Notifications')]
class extends Component {    
public array $preferences = [];
    public bool $saved = false;

    public function mount(): void
    {
        $this->preferences = Auth::user()->notification_preferences ?? [];
    }

    public function save(): void
    {
        $user = Auth::user();
        $user->notification_preferences = $this->preferences;
        $user->save();

        $this->saved = true;
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Notification Preferences')"
        :subheading="__('Control which email notifications you receive.')">

        @if ($saved)
            <div class="mb-4 text-sm text-green-600">
                {{ __('Preferences saved successfully.') }}
            </div>
        @endif

        <form wire:submit="save" class="space-y-6 mt-4">

            <label class="flex items-center space-x-3">
                <input type="checkbox" wire:model.live="preferences.real_world_confirmation"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                <span>{{ __('Email me when someone confirms me') }}</span>
            </label>

            <label class="flex items-center space-x-3">
                <input type="checkbox" wire:model.live="preferences.verification_reviewed"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                <span>{{ __('Email me when my verification is reviewed') }}</span>
            </label>

            <label class="flex items-center space-x-3">
                <input type="checkbox" wire:model.live="preferences.report_resolved"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                <span>{{ __('Email me when my reports are reviewed') }}</span>
            </label>

            <label class="flex items-center space-x-3">
                <input type="checkbox" wire:model.live="preferences.real_world_confirmation_request"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                <span>{{ __('Email me when someone requests a confirmation') }}</span>
            </label>

            <div class="pt-6 flex items-center gap-4">
                <flux:button variant="primary" type="submit">{{ __('Save Preferences') }}</flux:button>
                <x-action-message on="saved">{{ __('Saved.') }}</x-action-message>
            </div>
        </form>
    </x-settings.layout>
</section>


