<div>
    @if($show)
    {{-- Modal Overlay and Structure --}}
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 text-center md:items-center md:p-0">
            {{-- Background overlay --}}
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"
                wire:click="closeModal"></div>

            {{-- Modal panel --}}
            <div
                class="w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-zinc-800 shadow-xl rounded-2xl relative z-50">
                {{-- Modal Header --}}
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100" id="modal-title">
                        {{ __('Edit User') }}
                    </h3>
                    <button
                        class="text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400 focus:outline-none"
                        wire:click="closeModal">
                        <span class="sr-only">{{ __('Close modal') }}</span>
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                {{-- Edit User Form --}}
                <form wire:submit.prevent="saveUser" class="mt-4">
                    {{-- Firstname --}}
                    <div class="mb-4">
                        <label for="firstname" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{
                            __('Firstname') }}</label>
                        <input type="text" wire:model="firstname" id="firstname"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-neutral-700 dark:border-neutral-600 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        @error('firstname') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Lastname --}}
                    <div class="mb-4">
                        <label for="lastname" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{
                            __('Lastname') }}</label>
                        <input type="text" wire:model="lastname" id="lastname"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-neutral-700 dark:border-neutral-600 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        @error('lastname') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{
                            __('Email') }}</label>
                        <input type="email" wire:model="email" id="email"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-neutral-700 dark:border-neutral-600 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Roles --}}
                    <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Admin Checkbox --}}
                        <div>
                            <label for="is_admin" class="inline-flex items-center">
                                <input type="checkbox" wire:model="is_admin" id="is_admin"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-neutral-700 dark:border-neutral-600 dark:text-indigo-500">
                                <span class="ms-2 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Admin')
                                    }}</span>
                            </label>
                            @error('is_admin') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        {{-- Moderator Checkbox --}}
                        <div>
                            <label for="is_moderator" class="inline-flex items-center">
                                <input type="checkbox" wire:model="is_moderator" id="is_moderator"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-neutral-700 dark:border-neutral-600 dark:text-indigo-500">
                                <span class="ms-2 text-sm font-medium text-gray-700 dark:text-gray-300">{{
                                    __('Moderator') }}</span>
                            </label>
                            @error('is_moderator') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Section: Ban Status --}}
                    <div class="mt-6 border-t border-gray-200 dark:border-neutral-700 pt-6">
                        <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Ban Status') }}
                        </h4>

                        {{-- Is Banned Checkbox --}}
                        <div class="mb-4">
                            <label for="is_banned" class="inline-flex items-center">
                                {{-- Use wire:model.live to update is_banned property immediately --}}
                                <input type="checkbox" wire:model.live="is_banned" id="is_banned"
                                    class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50 dark:bg-neutral-700 dark:border-neutral-600 dark:text-red-500">
                                <span class="ms-2 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Is
                                    Banned') }}</span>
                            </label>
                            @error('is_banned') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Banned Until Date Picker - only visible if is_banned is true --}}
                        @if ($is_banned)
                        <div class="mb-4">
                            <label for="banned_until"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Banned Until')
                                }}</label>
                            <input type="date" wire:model="banned_until" id="banned_until"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-neutral-700 dark:border-neutral-600 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @error('banned_until') <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Banned Reason Textarea - only visible if is_banned is true --}}
                        <div class="mb-4">
                            <label for="banned_reason"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Ban Reason')
                                }}</label>
                            <textarea wire:model="banned_reason" id="banned_reason" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-neutral-700 dark:border-neutral-600 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
                            @error('banned_reason') <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        @endif
                    </div>

                    {{-- Section: Ban History --}}
                    @if($banHistory && $banHistory->count() > 0)
                    <div class="mt-6 border-t border-gray-200 dark:border-neutral-700 pt-6">
                        <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Ban History') }}
                        </h4>
                        <div class="space-y-3 max-h-48 overflow-y-auto"> {{-- Scrollable area --}}
                            @foreach($banHistory as $history)
                            <div
                                class="text-xs border border-gray-200 dark:border-neutral-600 rounded p-2 bg-gray-50 dark:bg-neutral-700/50">
                                <p><strong>{{ __('Banned At:') }}</strong> {{ $history->banned_at->format('Y-m-d H:i')
                                    }}</p>
                                <p><strong>{{ __('Expires At:') }}</strong> {{ $history->expires_at ?
                                    $history->expires_at->format('Y-m-d H:i') : 'Permanent' }}</p>
                                <p><strong>{{ __('Reason:') }}</strong> {{ $history->reason ?: 'N/A' }}</p>
                                <p><strong>{{ __('Banned By:') }}</strong> {{ $history->banner->name ?? 'System/Unknown'
                                    }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Form Actions --}}
                    <div class="mt-6 flex justify-end">
                        <button type="button"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-neutral-700 rounded-md hover:bg-gray-300 dark:hover:bg-neutral-600 focus:outline-none focus-visible:ring focus-visible:ring-gray-500 focus-visible:ring-opacity-50"
                            wire:click="closeModal">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit"
                            class="ms-3 inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus-visible:ring focus-visible:ring-indigo-500 focus-visible:ring-opacity-50 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                            {{ __('Save Changes') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>