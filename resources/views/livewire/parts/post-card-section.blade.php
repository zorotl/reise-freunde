{{-- This component displays a single post card within the feed. --}}
{{-- It receives a single $post object as a property. --}}
<div class="border border-gray-200 dark:border-neutral-700 rounded-lg p-4
    @if (!$post->is_active) bg-red-100 dark:bg-red-900/30 opacity-75 @endif">
    {{-- Post Header: Author, Timestamp, Location --}}
    <div class="flex items-start space-x-3">
        {{-- Author Avatar Placeholder --}}
        <div class="flex-shrink-0">
            <span class="inline-block h-8 w-8 rounded-full overflow-hidden bg-gray-200 dark:bg-neutral-700">
                {{-- User image --}}
                <span
                    class="flex h-full w-full items-center justify-center font-medium text-gray-600 dark:text-gray-300 text-xs">
                    <img class="h-full w-full rounded-lg object-cover" src="{{ $post->user->profilePictureUrl() }}"
                        alt="{{ $post->user->additionalInfo?->username ?? 'na' }}" />
                </span>
            </span>
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                {{-- Link to author's profile --}}
                <a href="{{ route('user.profile', $post->user) }}" class="hover:underline" wire:navigate>
                    {{ $post->user->additionalInfo?->username }}</a>
            </p>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{-- Link to the single post view --}}
                <time datetime="{{ $post->created_at->toIso8601String() }}">
                    {{ $post->created_at->diffForHumans()}}
                </time>
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
        <h3 class="mt-2 text-lg font-semibold text-gray-900 dark:text-gray-100">
            @if ($show === 'my' && !$post->is_active)
            <span class="text-red-900 font-bold">[Inactive]</span>
            @elseif ($post->expiry_date && $post->expiry_date->lessThan($now))
            <span class="text-red-900 font-bold">[Expired]</span>
            @endif
            {{ $post->title }}
        </h3>
        <p class="mt-1 text-sm text-gray-700 dark:text-gray-300 space-y-2">
            @if ($show === 'one')
            {{ $post->content }} {{-- Full content for single post view --}}
            @else
            {{ Str::limit($post->content, 300) }} {{-- Limit content length --}}
            @endif
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
        @unless ($show === 'my' || auth()->id() === $post->user_id)
        <a href="{{ route('mail.compose', ['receiverId' => $post->user->id, 'fixReceiver' => true]) }}" wire:navigate
            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            {{ __('Send Message') }}
        </a>
        @endunless

        {{-- NEW: Report Button (show if not owner and logged in) --}}
        @auth
        @if (auth()->id() !== $post->user_id)
        <button type="button"
            wire:click="$dispatch('openReportModal', { postId: {{ $post->id }}, postTitle: '{{ addslashes($post->title) }}' })"
            class="inline-flex items-center px-3 py-1.5 border border-red-300 dark:border-red-700 text-xs font-medium rounded-md text-red-700 dark:text-red-300 bg-white dark:bg-neutral-700 hover:bg-red-50 dark:hover:bg-red-900/50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
            <flux:icon.flag class="h-3 w-3 mr-1" /> {{-- Assuming a flag icon exists --}}
            {{ __('Report') }}
        </button>
        @endif
        @endauth
    </div>

    {{-- Management Buttons (Edit, Activate/Deactivate, Delete) - Only for Post Author --}}
    @if (auth()->id() === $post->user_id)
    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-neutral-700 flex space-x-2">
        {{-- Edit Button --}}
        <a wire:navigate href="{{ route('post.edit', ['id' => $post->id, 'origin' => $show]) }}"
            class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-neutral-600 text-xs font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-neutral-700 hover:bg-gray-50 dark:hover:bg-neutral-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            {{ __('Edit') }}
        </a>

        {{-- Activate/Deactivate Button --}}
        {{-- Pass the post ID to the Livewire action --}}
        <button wire:click="toggleActive({{ $post->id }})" wire:loading.attr="disabled"
            wire:target="toggleActive({{ $post->id }})" class="inline-flex items-center px-3 py-1.5 border text-xs font-medium rounded-md shadow-sm
                        @if ($post->is_active)
                            border-transparent text-white bg-green-600 hover:bg-green-700 focus:ring-green-500
                        @else
                            border-gray-300 dark:border-neutral-600 text-gray-700 dark:text-gray-200 bg-white dark:bg-neutral-700 hover:bg-gray-50 dark:hover:bg-neutral-600 focus:ring-indigo-500
                        @endif
                        focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50">
            <span wire:loading wire:target="toggleActive({{ $post->id }})" class="mr-1 -ml-0.5">
                <flux:icon.loading class="w-3 h-3" />
            </span>
            {{ $post->is_active ? __('Deactivate') : __('Activate') }}
        </button>

        {{-- Delete Button --}}
        {{-- Pass the post ID to the Livewire action --}}
        <button wire:click="deleteEntry({{ $post->id }})"
            wire:confirm="{{ __('Are you sure you want to delete this post?') }}" wire:loading.attr="disabled"
            wire:target="deleteEntry({{ $post->id }})"
            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50">
            <span wire:loading wire:target="deleteEntry({{ $post->id }})" class="mr-1 -ml-0.5">
                <flux:icon.loading class="w-3 h-3" />
            </span>
            {{ __('Delete') }}
        </button>
    </div>
    @endif
</div>