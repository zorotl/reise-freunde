{{-- This component displays a single post card within the feed. --}}
{{-- It receives a single $post object as a property. --}}
<div class="border border-gray-200 dark:border-neutral-700 rounded-lg p-4
    @if (!$post->is_active) bg-red-100 dark:bg-red-900/30 opacity-75 @endif">
    {{-- Post Header: Author, Timestamp, Location --}}
    <div class="flex items-start space-x-3">
        {{-- Author Avatar Placeholder --}}
        <div class="flex-shrink-0">
            <span class="inline-block h-8 w-8 rounded-full overflow-hidden bg-gray-200 dark:bg-neutral-700">
                {{-- Add user image later if available --}}
                <span
                    class="flex h-full w-full items-center justify-center font-medium text-gray-600 dark:text-gray-300 text-xs">{{
                    $post->user->initials() }}</span>
            </span>
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                {{-- Link to author's profile --}}
                <a href="{{ route('user.profile', $post->user) }}" class="hover:underline" wire:navigate>{{
                    $post->user->name }}</a>
                @if($post->user->additionalInfo?->username)
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ '@' . $post->user->additionalInfo->username
                    }}</span>
                @endif
            </p>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{-- Link to the single post view --}}
                <a href="{{ route('post.single', $post) }}" class="hover:underline" wire:navigate>
                    <time datetime="{{ $post->created_at->toIso8601String() }}">{{ $post->created_at->diffForHumans()
                        }}</time>
                </a>
                @if ($post->country || $post->city)
                <span class="mx-1">&middot;</span>
                <span>
                    {{ $this->countryList[$post->country] ?? $post->country ?? '' }}
                    {{ ($post->country && $post->city) ? ' / ' : '' }}
                    {{ $post->city ?? '' }}
                </span>
                @endif
            </p>
        </div>
    </div>

    {{-- Post Content --}}
    <a href="{{ route('post.single', $post) }}" wire:navigate>
        <h3 class="mt-2 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $post->title }}</h3>
        <p class="mt-1 text-sm text-gray-700 dark:text-gray-300 space-y-2">
            {{ Str::limit($post->content, 200) }} {{-- Limit content length --}}
        </p>
    </a>

    {{-- Travel Dates --}}
    @if ($post->from_date || $post->to_date)
    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
        {{ $post->from_date?->format('d M Y') }} - {{ $post->to_date?->format('d M Y') }}
    </p>
    @endif

    {{-- Buttons Section --}}
    <div class="mt-4 flex space-x-2">
        {{-- Button to go to the post author's profile --}}
        <a href="{{ route('user.profile', $post->user) }}" wire:navigate
            class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-neutral-600 text-xs font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-neutral-700 hover:bg-gray-50 dark:hover:bg-neutral-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            {{ __('View Profile') }} {{-- Or 'Go to Profile' --}}
        </a>

        {{-- Button to send a message to the post author --}}
        {{-- Assuming a route like 'mail.compose' that accepts a recipient user ID --}}
        <a href="{{ route('mail.compose', ['recipient' => $post->user->id]) }}" wire:navigate
            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            {{ __('Send Message') }}
        </a>
    </div>
</div>