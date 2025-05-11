<div class="max-w-6xl mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold mb-6">{{ __('Submitted Verifications') }}</h1>

    @if ($verifications->isEmpty())
        <p class="text-gray-600">{{ __('No pending verifications.') }}</p>
    @else
        <div class="space-y-6">
            @foreach ($verifications as $verification)
                <div class="p-4 bg-white shadow rounded border">
                    <h2 class="text-xl font-semibold mb-2">{{ $verification->user->name }}</h2>
                    <p class="text-gray-600 mb-2"><strong>{{ __('Email') }}:</strong> {{ $verification->user->email }}</p>

                    @if ($verification->id_document_path)
                        <p class="mb-2">
                            <strong>{{ __('ID Document') }}:</strong>
                            <a href="{{ asset('storage/' . $verification->id_document_path) }}" target="_blank" class="text-blue-600 underline">
                                {{ __('View uploaded file') }}
                            </a>
                        </p>
                    @endif

                    @if ($verification->social_links)
                        <p class="mb-2">
                            <strong>{{ __('Social Links') }}:</strong>
                            <ul class="list-disc ml-6">
                                @foreach ($verification->social_links as $link)
                                    <li><a href="{{ $link }}" class="text-blue-600 underline" target="_blank">{{ $link }}</a></li>
                                @endforeach
                            </ul>
                        </p>
                    @endif

                    <p class="mb-4"><strong>{{ __('Note') }}:</strong><br>{{ $verification->note }}</p>

                    <div class="flex gap-2">
                        <button wire:click="approve({{ $verification->id }})" class="bg-green-600 hover:bg-green-700 text-white px-4 py-1 rounded">
                            {{ __('Approve') }}
                        </button>
                        <button wire:click="reject({{ $verification->id }})" class="bg-red-600 hover:bg-red-700 text-white px-4 py-1 rounded">
                            {{ __('Reject') }}
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
