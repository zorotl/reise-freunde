<div>
    {{-- Main content area --}}
    <div class="py-8 w-full"> {{-- Added w-full --}}

        {{-- Welcome Message --}}
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100 mb-6">
            {{ __('Welcome back, :name!', ['name' => $user->firstname]) }}
        </h1>

        {{-- Status message for follow actions --}}
        @if (session()->has('status'))
        <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-900 dark:text-green-300"
            role="alert">
            {{ session('status') }}
        </div>
        @endif

        {{-- Grid for Dashboard Sections --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Main Content Area (Feed) - Spans 2 columns on large screens --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Feed Section --}}
                <section class="bg-white dark:bg-neutral-800 shadow sm:rounded-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-200 mb-4">{{ __('Feed') }}</h2>
                    <div class="space-y-4">
                        @forelse ($feedPosts as $post)
                        {{-- Reusable Post Card Component (Simplified without FluxUI Card) --}}
                        <div class="border border-gray-200 dark:border-neutral-700 rounded-lg p-4
                                @if (!$post->is_active) bg-red-100 dark:bg-red-900/30 opacity-75 @endif"
                            wire:key="post-{{ $post->id }}">
                            <div class="flex items-start space-x-3">
                                {{-- Author Avatar Placeholder --}}
                                <div class="flex-shrink-0">
                                    <span
                                        class="inline-block h-8 w-8 rounded-full overflow-hidden bg-gray-200 dark:bg-neutral-700">
                                        {{-- Add user image later if available --}}
                                        <span
                                            class="flex h-full w-full items-center justify-center font-medium text-gray-600 dark:text-gray-300">{{
                                            $post->user->initials() }}</span>
                                    </span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        <a href="{{ route('user.profile', $post->user->id) }}" class="hover:underline"
                                            wire:navigate>{{ $post->user->name }}</a>
                                        @if($post->user->additionalInfo?->username)
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ '@' .
                                            $post->user->additionalInfo->username }}</span>
                                        @endif
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        <a href="{{ route('post.single', $post->id) }}" class="hover:underline"
                                            wire:navigate>
                                            <time datetime="{{ $post->created_at->toIso8601String() }}">{{
                                                $post->created_at->diffForHumans() }}</time>
                                        </a>
                                        @if ($post->country || $post->city)
                                        <span class="mx-1">&middot;</span>
                                        <span>{{ $post->country ?? '' }}{{ $post->country && $post->city ? ' / ' :
                                            ''
                                            }}{{ $post->city ?? '' }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('post.single', $post->id) }}" wire:navigate>
                                <h3 class="mt-2 text-lg font-semibold text-gray-900 dark:text-gray-100">{{
                                    $post->title
                                    }}</h3>
                                <p class="mt-1 text-sm text-gray-700 dark:text-gray-300 space-y-2">
                                    {{ Str::limit($post->content, 200) }} {{-- Limit content length --}}
                                </p>
                            </a>
                            @if ($post->from_date || $post->to_date)
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                {{ $post->from_date?->format('d M Y') }} - {{ $post->to_date?->format('d M Y') }}
                            </p>
                            @endif
                        </div>
                        @empty
                        <p class="text-gray-500 dark:text-gray-400">{{ __('Your feed is empty. Follow some users or
                            create your own post!') }}</p>
                        @endforelse
                        {{-- Consider adding pagination or load more later --}}
                    </div>
                </section>
            </div>

            {{-- Sidebar Area (Notifications, Suggestions, Counts) - Spans 1 column on large screens --}}
            <div class="lg:col-span-1 space-y-6">

                {{-- Notifications Section (Follow Requests) --}}
                <section class="bg-white dark:bg-neutral-800 shadow sm:rounded-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-200 mb-4">{{ __('Notifications')
                        }}
                    </h2>
                    @if($pendingRequests->count() > 0)
                    <ul class="space-y-3">
                        @foreach($pendingRequests as $requestUser)
                        <li wire:key="request-{{ $requestUser->id }}" class="flex items-center justify-between text-sm">
                            <div class="flex items-center gap-2">
                                <span
                                    class="inline-block h-6 w-6 rounded-full overflow-hidden bg-gray-200 dark:bg-neutral-700">
                                    <span
                                        class="flex h-full w-full items-center justify-center font-medium text-gray-600 dark:text-gray-300 text-xs">{{
                                        $requestUser->initials() }}</span>
                                </span>
                                <a href="{{ route('user.profile', $requestUser->id) }}"
                                    class="font-medium text-indigo-600 hover:underline dark:text-indigo-400"
                                    wire:navigate>
                                    {{ $requestUser->name }}
                                </a>
                                <span class="text-gray-600 dark:text-gray-400">wants to follow you.</span>
                            </div>
                            {{-- Link to the full requests page --}}
                            <a href="{{ route('user.follow-requests') }}"
                                class="text-xs text-indigo-600 hover:underline dark:text-indigo-400" wire:navigate>
                                View
                            </a>
                        </li>
                        @endforeach
                        @if($user->pendingFollowerRequests()->count() > $pendingRequests->count())
                        <li class="text-center text-sm mt-3">
                            <a href="{{ route('user.follow-requests') }}"
                                class="text-indigo-600 hover:underline dark:text-indigo-400" wire:navigate>
                                View all requests...
                            </a>
                        </li>
                        @endif
                    </ul>
                    @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No new notifications.') }}</p>
                    @endif
                </section>

                {{-- User Suggestions Section --}}
                <section class="bg-white dark:bg-neutral-800 shadow sm:rounded-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-200 mb-4">{{ __('Suggested Users')
                        }}
                    </h2>
                    @if($suggestedUsers->count() > 0)
                    <ul class="space-y-4">
                        @foreach($suggestedUsers as $suggested)
                        <li wire:key="suggest-{{ $suggested->id }}" class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span
                                    class="inline-block h-8 w-8 rounded-full overflow-hidden bg-gray-200 dark:bg-neutral-700">
                                    <span
                                        class="flex h-full w-full items-center justify-center font-medium text-gray-600 dark:text-gray-300">{{
                                        $suggested->initials() }}</span>
                                </span>
                                <div>
                                    <a href="{{ route('user.profile', $suggested->id) }}"
                                        class="text-sm font-medium text-gray-900 dark:text-gray-100 hover:underline"
                                        wire:navigate>{{ $suggested->name }}</a>
                                    @if ($suggested->shared_hobbies_count + $suggested->shared_travel_styles_count >
                                    0)
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $suggested->shared_hobbies_count + $suggested->shared_travel_styles_count
                                        }}
                                        shared interest(s)
                                    </p>
                                    @endif
                                </div>
                            </div>
                            <button wire:click="followUser({{ $suggested->id }})" wire:loading.attr="disabled"
                                wire:target="followUser({{ $suggested->id }})"
                                class="inline-flex items-center px-2.5 py-1 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                                <span wire:loading wire:target="followUser({{ $suggested->id }})" class="mr-1 -ml-0.5">
                                    <flux:icon.loading />
                                </span>
                                {{ $suggested->isPrivate() ? __('Request') : __('Follow') }}
                            </button>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No suggestions right now. Add more
                        hobbies and travel styles to your profile!') }}</p>
                    @endif
                </section>

                {{-- Follower/Following Counts Section --}}
                <section class="bg-white dark:bg-neutral-800 shadow sm:rounded-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-200 mb-4">{{ __('Your Network') }}
                    </h2>
                    <div class="flex justify-around">
                        <a href="{{ route('user.followers', $user->id) }}" wire:navigate
                            class="text-center hover:text-indigo-600 dark:hover:text-indigo-400">
                            <span class="block text-2xl font-bold text-gray-900 dark:text-gray-100">{{
                                $followerCount
                                }}</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('Followers') }}</span>
                        </a>
                        <a href="{{ route('user.following', $user->id) }}" wire:navigate
                            class="text-center hover:text-indigo-600 dark:hover:text-indigo-400">
                            <span class="block text-2xl font-bold text-gray-900 dark:text-gray-100">{{
                                $followingCount
                                }}</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('Following') }}</span>
                        </a>
                    </div>
                </section>

            </div>
        </div>
    </div>
</div>