<div class="py-8" x-data="{ search: @entangle('search').live, showResults: @entangle('showResults').live }">
    <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100 mb-6">
        {{ __('Compose Message') }}
    </h1>

    {{-- Mail Tab Switcher --}}
    <x-mail-tab-switcher />

    {{-- Compose Form --}}
    <div class="bg-white dark:bg-neutral-800 shadow sm:rounded-lg overflow-hidden">
        <form wire:submit="sendMessage" class="p-4 sm:p-6 space-y-6 relative">
            {{-- Recipient --}}
            <div>
                <label for="search-input" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{__('To')}}</label>
                @if (!$fixReceiver || !$receiver_id)
                    <div class="relative">
                        <input type="text" id="search-input"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-neutral-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-neutral-700 dark:text-gray-300"
                            wire:model.live.debounce.300ms="search"
                            placeholder="{{ $selectedRecipientName ?: 'Search for a user...' }}"
                            @focus="showResults = true"
                            @blur.debounce.500ms="if (!document.querySelector('[data-recipient-item]:hover')) { showResults = false }"
                            autocomplete="off"
                            {{ $receiver_id && $fixReceiver ? 'readonly' : '' }}
                            {{ $receiver_id && !$fixReceiver && $selectedRecipientName ? 'disabled' : '' }}
                        />
                        @if ($receiver_id && !$fixReceiver && $selectedRecipientName)
                            <button type="button" wire:click="deselectRecipient" class="absolute inset-y-0 right-0 flex items-center pr-3 text-red-500 hover:text-red-700 focus:outline-none" aria-label="Deselect recipient">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        @endif
                    </div>
                @endif

                @if ($receiver_id && $selectedRecipientName)
                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Recipient:') }} <span class="font-semibold">{{ $selectedRecipientName }}</span>
                    </div>
                @endif

                @error('receiver_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                {{-- Search Results Dropdown --}}
                <div x-show="showResults && search.length >= 2 && $wire.searchResults.length > 0 && !$wire.receiver_id"
                    x-transition
                    class="absolute z-10 mt-1 w-full bg-white dark:bg-neutral-900 border border-gray-300 dark:border-neutral-700 rounded-md shadow-lg max-h-60 overflow-y-auto">
                    <ul>
                        @foreach ($searchResults as $user)
                            <li
                                class="px-4 py-2 hover:bg-indigo-50 dark:hover:bg-neutral-700 cursor-pointer flex items-center gap-3"
                                wire:click="selectRecipient({{ $user['id'] }}, '{{ addslashes($user['display_name']) }}')"
                                data-recipient-item>
                                
                                {{-- Avatar --}}
                                <img src="{{ $user['avatar_url'] ?? '/images/default-avatar.png' }}"
                                    alt="{{ $user['display_name'] }}"
                                    class="w-8 h-8 rounded-full object-cover" />

                                {{-- Name + Username --}}
                                <div class="text-sm">
                                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ $user['display_name'] }}</div>                                    
                                </div>
                            </li>
                        @endforeach                    
                    </ul>
                </div>
                 <div x-show="showResults && search.length >= 2 && $wire.searchResults.length === 0 && !$wire.receiver_id"
                    class="absolute z-10 mt-1 w-full bg-white dark:bg-neutral-900 border border-gray-300 dark:border-neutral-700 rounded-md shadow-lg p-4 text-sm text-gray-500 dark:text-gray-400">
                    No users found.
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

            {{-- Buttons --}}
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-neutral-700">
                <button type="button" onclick="history.back()"
                    class="inline-flex items-center px-4 py-2 border text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-neutral-700 dark:text-gray-300 dark:hover:bg-neutral-600 border-gray-300 dark:border-neutral-600">
                    {{ __('Cancel') }}
                </button>
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                    <flux:icon.paper-airplane class="w-4 h-4 mr-2" />
                    {{ __('Send Message') }}
                </button>
            </div>
        </form>
    </div>
</div>