<div>
    <div class="mb-4">
        <label for="search" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
            {{ __('Your travel buddy is waiting...') }}
        </label>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse ($posts as $post)
            <div class="bg-white dark:bg-neutral-800 rounded-lg shadow overflow-hidden">
                <div class="p-4">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-200">{{ $post->title }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mt-2">{{ Str::limit($post->content, 50) }}</p>
                    <a href="{{ route('post.single', ['post' => $post->id]) }}"
                        class="text-indigo-500 hover:text-indigo-700 mt-2 block text-sm">{{
                        __('Read More') }}</a>
                </div>
            </div>
            @empty
            <p class="text-gray-500 dark:text-gray-400">{{ __('No recent posts found.') }}</p>
            @endforelse
        </div>
    </div>
</div>