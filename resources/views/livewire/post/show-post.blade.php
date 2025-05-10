<div>
    @if (session()->has('error'))
    <div class="bg-red-200 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-bold">Error!</strong>
        <span class="block sm:inline">{{ session('error') }}</span>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
            <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20">
                <title>Close</title>
                <path fill-rule="evenodd"
                    d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.586l-2.651 3.263a1.2 1.2 0 0 1-1.697-1.697L8.303 10l-3.263-2.651a1.2 1.2 0 0 1 1.697-1.697L10 8.414l2.651-3.263a1.2 1.2 0 0 1 1.697 1.697L11.697 10l3.263 2.651a1.2 1.2 0 0 1 0 1.697z" />
            </svg>
        </span>
    </div>
    @endif

    <div class="mb-4">
        <flux:button onclick="history.back()" size="sm" variant="filled">
            Back
        </flux:button>
    </div>

    <div class="rounded-xl shadow-md overflow-hidden
    @if ($post->expiry_date && $post->expiry_date->lessThan($now)) bg-gray-300
    @elseif (!$post->is_active) bg-red-300
    @else bg-white dark:bg-neutral-700
    @endif">

        <section class="bg-white dark:bg-neutral-800 shadow sm:rounded-lg p-6">
            <div class="space-y-4">
                <livewire:parts.post-card-section :post="$post" :show="$show" wire:key="post-card-{{ $post->id }}" />
            </div>
        </section>
    </div>
</div>