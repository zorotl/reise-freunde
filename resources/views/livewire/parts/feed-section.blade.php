{{-- resources/views/livewire/feed-section.blade.php --}}
{{-- This component serves as the container for the feed posts. --}}
{{-- It receives a collection of $feedPosts and renders PostCardSection for each. --}}
<section class="bg-white dark:bg-neutral-800 shadow sm:rounded-lg p-6">
    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-200 mb-4">{{ __('Feed') }}</h2>
    <div class="space-y-4">
        @forelse ($feedPosts as $post)
        {{-- Include the individual post card component for each post --}}
        {{-- Pass the individual post object to the PostCardSection component --}}
        <livewire:parts.post-card-section :post="$post" :show="$show" wire:key="post-card-{{ $post->id }}" />
        @empty
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Your feed is empty. Follow some users or create your own post!') }}</p>
        @endforelse
        {{-- Consider adding pagination or load more later for the feed --}}
    </div>
</section>