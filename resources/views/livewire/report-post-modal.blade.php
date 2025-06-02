<div>
    @if ($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true"
        x-data="{ showModal: @entangle('showModal') }" x-show="showModal" x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            {{-- Background overlay --}}
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"
                @click="showModal = false; $wire.closeModal()"></div>

            {{-- This element is to trick the browser into centering the modal contents. --}}
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            {{-- Modal panel --}}
            <div x-show="showModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-zinc-800 shadow-xl rounded-2xl relative z-50"
                @click.away="showModal = false; $wire.closeModal()">
                {{-- Modal Header --}}
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100" id="modal-title">
                        {{ __('Report Post') }}: {{ $postTitle }}
                    </h3>
                    <button
                        class="text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400 focus:outline-none"
                        @click="showModal = false; $wire.closeModal()">
                        <span class="sr-only">{{ __('Close modal') }}</span>
                        <flux:icon.x-mark class="w-6 h-6" />
                    </button>
                </div>

                {{-- Report Form --}}
                <form wire:submit.prevent="submitReport" class="mt-4 space-y-4">
                    {{-- General Error Message Area --}}
                    @error('general')
                    <div
                        class="text-red-500 text-sm p-2 bg-red-100 dark:bg-red-900/50 rounded border border-red-300 dark:border-red-700">
                        {{ $message }}
                    </div>
                    @enderror

                    <div>
                        <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ __('Reason') }}
                        </label>
                        <select wire:model="reason" id="reason"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:bg-neutral-700 dark:border-neutral-600 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">{{ __('Select reason') }}</option>
                            @foreach ($availableReasons as $value)
                                <option value="{{ Str::after($value, 'report_reason.') }}">
                                    {{ __( $value) }}
                                </option>
                            @endforeach
                        </select>
                        @error('reason') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="comment" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ __('Additional details (optional)') }}
                        </label>
                        <textarea wire:model="comment" id="comment" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-neutral-700 dark:border-neutral-600 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            placeholder="{{ __('Provide details if needed...') }}"></textarea>
                        @error('comment') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Form Actions --}}
                    <div class="mt-6 flex justify-end space-x-3">
                        <flux:button type="button" variant="filled" @click="showModal = false; $wire.closeModal()">
                            {{ __('Cancel') }}
                        </flux:button>
                        <flux:button type="submit" variant="danger" wire:loading.attr="disabled">
                            <span wire:loading wire:target="submitReport" class="mr-2">
                                <flux:icon.loading />
                            </span>
                            {{ __('Submit Report') }}
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>