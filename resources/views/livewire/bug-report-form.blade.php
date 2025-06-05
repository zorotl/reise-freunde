<div>
  <h1 class="text-2xl font-bold mb-5 flex items-center gap-2">
    {{ __('Bug Report') }}
  </h1>
  <section class="w-full">
    <div class="flex items-start max-md:flex-col">
      <div class="flex-1 self-stretch max-md:pt-6">
        <div class="mt-5 w-full max-w-lg">
          <form wire:submit.prevent="submit" class="w-full space-y-5" novalidate>
            @csrf

            <flux:input
              wire:model="email"
              label="{{ __('Email') }}"
              type="email"
              id="bug_email"
              autocomplete="email"
              required
            />

            <div class="space-y-1">
              <flux:textarea
                wire:model="message"
                label="{{ __('Message') }}"
                id="bug_message"
                rows="3"
                required
              />            
            </div>

            <flux:input
              wire:model="url"
              label="{{ __('On which page does the error occur (URL or name)?') }}"
              type="text"
              id="bug_url"
              autocomplete="url"
            />       

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mt-6">
              <button type="button" onclick="window.history.back()"
                class="inline-flex items-center justify-center px-4 py-2 border text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-neutral-700 dark:text-gray-300 dark:hover:bg-neutral-600 border-gray-300 dark:border-neutral-600">
                {{ __('Cancel') }}
              </button>

              <flux:button type="submit" variant="primary" wire:loading.attr="disabled" class="w-full sm:w-auto">
                <span wire:loading wire:target="submit" class="mr-1">
                  <flux:icon.loading />
                </span>
                {{ __('Send') }}
              </flux:button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>
