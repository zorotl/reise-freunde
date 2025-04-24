<div>
    @if($show)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 text-center md:items-center md:p-0">
            {{-- Background overlay --}}
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"
                wire:click="closeModal"></div>

            {{-- Modal panel --}}
            <div
                class="w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-zinc-800 shadow-xl rounded-2xl relative z-50">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100" id="modal-title">
                        {{ __('Edit Post') }}
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

                {{-- Edit Post Form --}}
                <form wire:submit.prevent="savePost" class="mt-4">
                    {{-- Title --}}
                    <div class="mb-4">
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{
                            __('Title') }}</label>
                        <input type="text" wire:model="title" id="title"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-neutral-700 dark:border-neutral-600 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Content --}}
                    <div class="mb-4">
                        <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{
                            __('Content') }}</label>
                        <textarea wire:model="content" id="content" rows="4"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-neutral-700 dark:border-neutral-600 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
                        @error('content') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Dates and Status --}}
                    <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- From Date --}}
                        <div>
                            <label for="from_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{
                                __('From Date') }}</label>
                            <input type="date" wire:model="from_date" id="from_date"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-neutral-700 dark:border-neutral-600 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @error('from_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        {{-- To Date --}}
                        <div>
                            <label for="to_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{
                                __('To Date') }}</label>
                            <input type="date" wire:model="to_date" id="to_date"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-neutral-700 dark:border-neutral-600 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @error('to_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        {{-- Expiry Date --}}
                        <div class="col-span-1 md:col-span-2">
                            <label for="expiry_date"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Expiry Date')
                                }}</label>
                            <input type="date" wire:model="expiry_date" id="expiry_date"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-neutral-700 dark:border-neutral-600 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @error('expiry_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        {{-- Active Checkbox --}}
                        <div class="col-span-1 md:col-span-2">
                            <label for="is_active" class="inline-flex items-center">
                                <input type="checkbox" wire:model="is_active" id="is_active"
                                    class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50 dark:bg-neutral-700 dark:border-neutral-600 dark:text-green-500">
                                <span class="ms-2 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Is
                                    Active') }}</span>
                            </label>
                            @error('is_active') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Location --}}
                    <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Country --}}
                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{
                                __('Country') }}</label>
                            <input type="text" wire:model="country" id="country"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-neutral-700 dark:border-neutral-600 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @error('country') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        {{-- City --}}
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{
                                __('City') }}</label>
                            <input type="text" wire:model="city" id="city"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-neutral-700 dark:border-neutral-600 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @error('city') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>


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