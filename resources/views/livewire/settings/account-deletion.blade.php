<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

new 
#[Layout('components.layouts.app')] // Use the main app layout
#[Title('Settings - Delete Account')] // Set the page title
class extends Component {
    // You can add page title logic here if needed
    // public function mount(): void {}
}; ?>

<section class="w-full">
    @include('partials.settings-heading') {{-- Include the common settings heading --}}

    {{-- Use the settings layout component for consistent structure --}}
    <x-settings.layout :heading="__('Account Deletion')" :subheading="__('Permanently delete your account')">
        {{-- Include the delete user form component here --}}
        <livewire:settings.delete-user-form />
    </x-settings.layout>
</section>