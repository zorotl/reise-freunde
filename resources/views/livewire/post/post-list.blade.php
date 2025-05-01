<div>
    <div class="mb-5 flex justify-between items-center">
        @if ($show === 'all')
        <h2 class="text-xl font-semibold text-gray-900 dark:text-stone-400">Find your travel buddy</h2>
        @elseif ($show === 'my')
        <h2 class="text-xl font-semibold text-gray-900 dark:text-stone-400">My Posts</h2>
        @endif
        <a wire:navigate href="{{ route('post.create') }}"
            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
            Create New Entry
        </a>
    </div>

    @unless ($show === 'my')
    <div>
        <livewire:search />
    </div>
    @endunless

    <section class="bg-white dark:bg-neutral-800 shadow sm:rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-200 mb-4">{{ __('Test Titel') }}</h2>
        <div class="space-y-4">
            @forelse ($entries as $post)
            {{-- Include the individual post card component for each post --}}
            <livewire:parts.post-card-section :post="$post" :show="$show" wire:key="post-card-{{ $post->id }}" />
            @empty
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('Your feed is empty. Follow some users or create your own post!') }}
            </p>
            @endforelse
            {{-- Consider adding pagination or load more later for the feed --}}
        </div>
    </section>
</div>