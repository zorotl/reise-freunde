<div>
    <section class="w-full">
        <div class="flex items-start max-md:flex-col">
            <div class="flex-1 self-stretch max-md:pt-6">
                <div class="mt-5 w-full max-w-lg">
                    <form wire:submit="{{$action}}" class="my-6 w-full space-y-6">
                        @csrf
                        @if ($action === 'update')
                        @method('PUT')
                        @endif
                        <flux:input wire:model="title" :label="__('Title')" type="text" autofocus
                            autocomplete="title" />

                        <div x-data="{ content: @entangle('content'), min: 50 }" class="space-y-1">
                            <!-- Your existing textarea component -->
                            <flux:textarea x-model="content" wire:model="content" :label="__('Content')"
                                autocomplete="content" />

                            <!-- Remaining characters until minimum reached -->
                            <p x-text="
                                    content.length >= min
                                      ? 'Minimum length reached'
                                      : `${min - content.length} more characters needed`
                                  " class="text-sm" :class="{
                                    'text-green-600': content.length >= min,
                                    'text-gray-600': content.length < min
                                  }"></p>
                        </div>

                        <flux:input wire:model="expiryDate" :label="__('Expiry Date')" type="date"
                            autocomplete="expiryDate" />

                        <flux:input wire:model="fromDate" :label="__('From Date')" type="date"
                            autocomplete="fromDate" />

                        <flux:input wire:model="toDate" :label="__('To Date')" type="date" autocomplete="toDate" />

                        <flux:input wire:model="country" :label="__('Country (optional)')" type="text"
                            autocomplete="country" />

                        <flux:input wire:model="city" :label="__('City (optional)')" type="text" autocomplete="city" />

                        <div class="flex items-center gap-4">
                            <div class="flex items-center justify-end">
                                <flux:button variant="primary" type="submit" class="w-full">{{ __($buttonText) }}
                                </flux:button>
                            </div>
                            <a href="{{ route('post.show') }}" class="text-gray-500 hover:underline">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>