<div>
    <section class="w-full">
        <div class="flex items-start max-md:flex-col">
            <div class="flex-1 self-stretch max-md:pt-6">
                <div class="mt-5 w-full max-w-lg">
                    <form wire:submit="update" class="my-6 w-full space-y-6">
                        @csrf
                        @method('PUT')

                        <flux:input wire:model="title" :label="__('Title')" type="text" autofocus
                            autocomplete="title" />

                        <flux:textarea wire:model="content" :label="__('Content')" type="email"
                            autocomplete="content" />

                        <flux:input wire:model="expiryDate" :label="__('Expiry Date')" type="date"
                            autocomplete="expiryDate" />

                        <div class="flex items-center gap-4">
                            <div class="flex items-center justify-end">
                                <flux:button variant="primary" type="submit" class="w-full">{{ __('Save') }}
                                </flux:button>
                            </div>
                            <a href="{{ route('pinboard.show') }}" class="text-gray-500 hover:underline">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>