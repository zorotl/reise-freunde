{{-- resources/views/livewire/network-stats.blade.php --}}
{{-- This component displays the user's network statistics (followers/following). --}}
<section class="bg-white dark:bg-neutral-800 shadow sm:rounded-lg p-6">
    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-200 mb-4">{{ __('Your Network') }}</h2>
    <div class="flex justify-around">
        <a href="{{ route('user.followers', $user->id) }}" wire:navigate
            class="text-center hover:text-indigo-600 dark:hover:text-indigo-400">
            <span class="block text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $followerCount }}</span>
            <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('Followers') }}</span>
        </a>
        <a href="{{ route('user.following', $user->id) }}" wire:navigate
            class="text-center hover:text-indigo-600 dark:hover:text-indigo-400">
            <span class="block text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $followingCount }}</span>
            <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('Following') }}</span>
        </a>
    </div>
</section>