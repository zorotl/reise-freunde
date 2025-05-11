<section class="w-full">
    @include('partials.settings-heading')
    <x-settings.layout :heading="__('Profile Verification')" :subheading="__('Verify your Profile')">
        <div class="my-6">
            <h2 class="text-xl font-bold mb-4">{{ __('Verify Your Profile') }}</h2>

            @if (session('success'))
                <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <form wire:submit.prevent="submit" class="space-y-6">
                <div>
                    <label class="block font-medium">{{ __('Upload ID Document (optional)') }}</label>
                    <input type="file" wire:model="idDocument" class="mt-1" />
                </div>

                <div>
                    <label class="block font-medium">{{ __('Social Media Links (optional)') }}</label>
                    <textarea wire:model="socialLinks" class="w-full border rounded p-2" rows="3" placeholder="https://instagram.com/yourprofile"></textarea>
                </div>

                <div>
                    <label class="block font-medium">{{ __('Write something about yourself') }}</label>
                    <textarea wire:model="note" class="w-full border rounded p-2" rows="5"></textarea>
                </div>

                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    {{ __('Submit Verification') }}
                </button>
            </form>
        </div>
    </x-settings.layout>
</section>

