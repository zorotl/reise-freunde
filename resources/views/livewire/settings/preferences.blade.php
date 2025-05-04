<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Title;

new 
#[Title('Settings - Preferences')]
class extends Component {
    //
}; ?>


<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Preferences')" :subheading="__('Your travel styles, hobbies & interests')">
        <div class="my-6">
            <livewire:user.travel-style-preferences />
        </div>
    </x-settings.layout>
</section>