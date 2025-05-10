@props([
'user',
'showActions' => false,
])

@php
$isFollowing = auth()->user()?->isFollowing($user) ?? false;
@endphp

<div
    class="flex items-center gap-4 p-4 bg-white dark:bg-neutral-700 rounded-xl shadow-sm border border-gray-200 dark:border-neutral-600">
    {{-- Avatar --}}
    <img src="{{ $user->profilePictureUrl() ?? asset('images/default-avatar.png') }}" alt="{{ $user->username }} avatar"
        class="w-12 h-12 rounded-full object-cover border border-gray-300 dark:border-neutral-500">

    {{-- User Info --}}
    <div class="flex-1">
        <a href="{{ route('user.profile', $user) }}" class="hover:underline" wire:navigate>
            <b>{{ $user->additionalInfo?->username ?? 'username_not_set' }}</b>
        </a>
        <div class="text-sm text-gray-500 dark:text-gray-300">
            {{ \Carbon\Carbon::parse($user->additionalInfo?->birthday)->age }}
            {{ __('years old from') }}
            {{ $this->countryList[$user->additionalInfo->nationality] ?? $user->additionalInfo->nationality }}
        </div>
    </div>

    {{-- Actions --}}
    @if ($showActions && auth()->id() !== $user->id)
    <div class="flex gap-2">
        {{-- Follow/Unfollow --}}
        @if (auth()->user()->isFollowing($user))
        <button wire:click="unfollowUser({{ $user->id }})"
            class="px-3 py-1 text-sm rounded bg-red-500 text-white hover:bg-red-600">
            {{ __('Unfollow') }}
        </button>
        @else
        <button wire:click="followUser({{ $user->id }})"
            class="px-3 py-1 text-sm rounded bg-indigo-500 text-white hover:bg-indigo-600">
            {{ __('Follow') }}
        </button>
        @endif

        {{-- Message --}}
        <a href="{{ route('mail.compose', ['receiverId' => $user->id, 'fixReceiver' => true]) }}" wire:navigate
            class="px-3 py-1 text-sm rounded border border-gray-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-neutral-600">
            {{ __('Message') }}
        </a>
    </div>
    @endif
</div>