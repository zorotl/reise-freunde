<div>
    @auth
        @if(auth()->user()->is_approved && auth()->user()->hasVerifiedEmail())
            {{-- Assuming livewire:post.form-post does not use problematic x-syntax, or you'll adapt it --}}
            <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow p-4 sm:p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Create New Post') }}</h2>
                <livewire:post.form-post />
            </div>
        @endif
    @endauth

    <div class="space-y-6">
        @forelse ($posts as $post)
            {{-- Basic Post Card with Tailwind --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 sm:p-6">
                <div class="flex items-center mb-3">
                    <a href="{{ route('user.profile', $post->user->id) }}">
                        <img class="h-10 w-10 rounded-full object-cover mr-3"
                             src="{{ $post->user->avatar_url ?? asset('images/default-avatar.png') }}"
                             alt="{{ $post->user->username }}">
                    </a>
                    <div>
                        <a href="{{ route('user.profile', $post->user->id) }}"
                           class="text-sm font-semibold text-gray-900 dark:text-gray-100 hover:underline">
                            {{ $post->user->username }}
                        </a>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            <a href="{{ route('post.show', $post->slug) }}" class="hover:underline">
                                {{ $post->created_at->diffForHumans() }}
                            </a>
                            @if($post->is_edited)
                                <span>&middot; {{ __('edited') }}</span>
                            @endif
                        </p>
                    </div>
                    {{-- Post actions (edit/delete dropdown) can be added here if needed --}}
                </div>

                <a href="{{ route('post.show', $post->slug) }}" class="block mb-3">
                    @if ($post->title)
                         <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 hover:underline mb-1">{{ $post->title }}</h3>
                    @endif
                    <p class="text-gray-700 dark:text-gray-300 text-sm whitespace-pre-wrap">{{ Str::limit($post->content, 250) }}</p>
                    @if(strlen($post->content) > 250)
                        <span class="text-indigo-600 dark:text-indigo-400 text-sm hover:underline">{{ __('Read more') }}</span>
                    @endif
                </a>

                @if ($post->image_path)
                    <a href="{{ route('post.show', $post->slug) }}">
                        <img src="{{ Storage::url($post->image_path) }}" alt="{{ $post->title ?? 'Post image' }}"
                             class="rounded-lg max-h-96 w-full object-contain mb-3">
                    </a>
                @endif

                {{-- Likes and Comments - assuming you have these Livewire components or will build them --}}
                <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                    <div>
                        {{-- Example: @livewire('post.like-button', ['post' => $post], key('like-'.$post->id)) --}}
                        <span>{{ $post->likes_count ?? $post->likes()->count() }} {{ __('Likes') }}</span>
                    </div>
                    <div>
                        <span>{{ $post->comments_count ?? $post->comments()->count() }} {{ __('Comments') }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-gray-500 dark:text-gray-400 py-10">
                <svg class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="mt-2 text-sm">{{ __('No posts to display yet.') }}</p>
            </div>
        @endforelse

        @if($posts->hasPages())
            <div class="mt-6">
                {{ $posts->links() }}
            </div>
        @endif
    </div>
</div>