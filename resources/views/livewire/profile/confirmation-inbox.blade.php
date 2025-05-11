<div class="py-8">
    <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100 mb-6">
        {{ __('Incoming Confirmations') }} {{-- Adjust for each page --}}
    </h1>

    {{-- Tab Switcher --}}
    <x-user-tab-switcher />

    @forelse ($requests as $req)
        <div class="mb-4 border p-3 rounded">
            <p class="mb-2">                
                {{ $req->requester->name }} ({{ $req->requester->email }})
                <br>
                {{ __('wants to confirm you met in real life.') }}
            </p>
            <div class="flex gap-2">
                <button wire:click="accept({{ $req->id }})" class="bg-green-600 text-white px-3 py-1 rounded">
                    {{ __('Accept') }}
                </button>
                <button wire:click="reject({{ $req->id }})" class="bg-red-600 text-white px-3 py-1 rounded">
                    {{ __('Reject') }}
                </button>
            </div>
        </div>

    @empty
        <p class="text-gray-600">{{ __('No incoming confirmations.') }}</p>
    @endforelse
</div>
