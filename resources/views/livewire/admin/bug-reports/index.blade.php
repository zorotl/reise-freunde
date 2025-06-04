<div class="max-w-6xl mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold mb-6">{{ __('Bug Reports') }}</h1>

    @if ($reports->isEmpty())
        <p class="text-gray-600">{{ __('No pending bug reports.') }}</p>
    @else
        <div class="space-y-6">
            @foreach ($reports as $report)
                <div class="bg-white shadow rounded border p-4">
                    <p class="mb-2 text-sm text-gray-500">{{ $report->created_at->diffForHumans() }} - {{ $report->url }}</p>
                    <p class="mb-2">{{ $report->message }}</p>
                    <p class="mb-4 text-sm text-gray-600">{{ $report->email }}</p>
                    <div class="flex gap-2">
                        <button wire:click="accept({{ $report->id }})" class="bg-green-600 hover:bg-green-700 text-white px-4 py-1 rounded">{{ __('Accept') }}</button>
                        <button wire:click="reject({{ $report->id }})" class="bg-red-600 hover:bg-red-700 text-white px-4 py-1 rounded">{{ __('Reject') }}</button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
