<div class="mt-6">
    <form wire:submit.prevent="submit" class="space-y-2 text-left">
        <div>
            <label for="bug_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
            <input type="email" id="bug_email" wire:model="email" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-neutral-700 dark:border-neutral-600 dark:text-gray-300" />
        </div>
        <div>
            <label for="bug_message" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Message</label>
            <textarea id="bug_message" rows="3" wire:model="message" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-neutral-700 dark:border-neutral-600 dark:text-gray-300"></textarea>
            @error('message') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <input type="hidden" wire:model="url" />
        <div class="text-right">
            <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                <span wire:loading wire:target="submit" class="mr-1"><flux:icon.loading /></span>
                {{ __('Send') }}
            </flux:button>
        </div>
    </form>
</div>
