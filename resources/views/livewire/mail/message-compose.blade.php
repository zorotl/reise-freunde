<div class="py-8" x-data="{ search: @entangle('search').live, showResults: false }">
    <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100 mb-6">
        {{ __('Compose Message') }}
    </h1>

    {{-- Mail Tab Switcher --}}
    <x-mail-tab-switcher />

    {{-- Compose Form --}}
    <div class="bg-white dark:bg-neutral-800 shadow sm:rounded-lg overflow-hidden">
        <form wire:submit="sendMessage" class="p-4 sm:p-6 space-y-6 relative">
            {{-- Recipient Search --}}
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{__('To')}}</label>
                <div x-show="!$wire.receiver_id">
                    <input type="text" id="search"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-neutral-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-neutral-700 dark:text-gray-300"
                        x-model="search" @focus="showResults = !@json($receiver_id)"
                        @blur.debounce.200ms="showResults = false"
                        @input.debounce.200ms="showResults = search.length >= 2 && !@json($receiver_id)" {{ $receiver_id
                        ? 'readonly' : '' }} />
                </div>

                <div x-show="$wire.receiver_id" class="mt-1 flex items-center">
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('Recipient:') }} {{ $selectedRecipientName }}
                    </div>
                    @unless ($fixReceiver)
                    <button type="button" wire:click="deselectRecipient"
                        class="ml-2 text-red-500 hover:text-red-700 focus:outline-none">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    @endunless
                </div>

                @error('receiver_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                {{-- Search Results Dropdown --}}
                <div x-show="showResults && search.length >= 2 && $wire.searchResults.length > 0 && !$wire.receiver_id"
                    class="absolute z-10 mt-1 w-full bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-700 rounded-md shadow-lg">
                    <ul class="max-h-48 overflow-y-auto">
                        @foreach ($searchResults as $user)
                        <li class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-neutral-700 cursor-pointer"
                            wire:click="selectRecipient({{ $user['id'] }})">
                            {{ $user['name'] }}
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Subject --}}
            <div>
                <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('Subject') }}
                </label>
                <input type="text" wire:model="subject" id="subject"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-neutral-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-neutral-700 dark:text-gray-300">
                @error('subject') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- Message Body --}}
            <div>
                <label for="body" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('Message') }}
                </label>
                <textarea wire:model="body" id="body" rows="5"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-neutral-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-neutral-700 dark:text-gray-300"></textarea>
                @error('body') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- Action Buttons --}}
            <div class="flex justify-end gap-2">
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 dark:bg-indigo-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow-sm hover:bg-indigo-500 dark:hover:bg-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Send Message') }}
                </button>
                <button type="button" onclick="history.back()"
                    class="inline-flex items-center px-4 py-2 bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-700 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Cancel') }}
                </button>
            </div>
        </form>
    </div>
</div>