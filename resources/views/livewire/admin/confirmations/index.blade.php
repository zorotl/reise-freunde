<div class="max-w-6xl mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold mb-6">{{ __('Bürgschaft Confirmations') }}</h1>

    @if ($confirmations->isEmpty())
        <p class="text-gray-600">{{ __('No pending confirmations.') }}</p>
    @else
        <div class="space-y-6">
            @foreach ($confirmations as $c)
                <div class="bg-white shadow rounded border p-4">
                    <p class="mb-2">
                        <strong>{{ $c->requester->name }}</strong> →
                        <strong>{{ $c->confirmer->name }}</strong>
                    </p>
                    <p class="text-sm text-gray-500 mb-4">
                        {{ __('Requested on') }}: {{ $c->created_at->diffForHumans() }}
                    </p>
                    <div class="flex gap-2">
                        <button wire:click="approve({{ $c->id }})"
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-1 rounded">
                            {{ __('Approve') }}
                        </button>
                        <button wire:click="reject({{ $c->id }})"
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-1 rounded">
                            {{ __('Reject') }}
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
