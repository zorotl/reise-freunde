<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="bg-white dark:bg-neutral-800 shadow sm:rounded-lg overflow-hidden">
            <div
                class="px-4 py-5 sm:px-6 bg-gray-50 dark:bg-neutral-900 border-b border-gray-200 dark:border-neutral-700 flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-4">
                    {{-- Profile Picture --}}
                    <div
                        class="rounded-full overflow-hidden w-16 h-16 sm:w-24 sm:h-24 bg-gray-300 flex items-center justify-center flex-shrink-0">
                        @if ($user->additionalInfo?->profile_picture)
                        {{-- Placeholder - Implement image storage/retrieval later --}}
                        {{-- <img src="{{ asset('storage/' . $user->additionalInfo->profile_picture) }}"
                            alt="Profile Picture" class="w-full h-full object-cover"> --}}
                        <span class="text-xl text-gray-600 dark:text-gray-400">{{ $user->initials() }}</span>
                        @else
                        <span class="text-xl text-gray-600 dark:text-gray-400">{{ $user->initials() }}</span>
                        @endif
                    </div>
                    {{-- Name & Counts --}}
                    <div>
                        <h2 class="text-lg sm:text-xl font-medium text-gray-900 dark:text-gray-100">
                            {{ $user->name }}
                        </h2>
                        @if ($user->additionalInfo?->username)
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ '@' . $user->additionalInfo->username }}
                            @if($user->isPrivate())
                            <flux:icon.lock-closed class="size-8" /> {{ __('Private Account') }}
                            @endif
                        </p>
                        @endif
                        {{-- Follower/Following Counts --}}
                        <div class="mt-2 flex space-x-4 text-sm text-gray-600 dark:text-gray-400">
                            <a href="{{ route('user.followers', $user->id) }}" wire:navigate class="hover:underline">
                                <span class="font-semibold text-gray-900 dark:text-gray-100">{{
                                    $this->user->followers_count }}</span> {{ __('Followers') }}
                            </a>
                            <a href="{{ route('user.following', $user->id) }}" wire:navigate class="hover:underline">
                                <span class="font-semibold text-gray-900 dark:text-gray-100">{{
                                    $this->user->following_count }}</span> {{ __('Following') }}
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex-shrink-0">
                    @if ($this->isOwnProfile)
                    <a wire:navigate href="{{ route('settings.profile') }}"
                        class="inline-flex items-center px-4 py-2 bg-white dark:bg-neutral-700 border border-gray-300 dark:border-neutral-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-neutral-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-neutral-800 disabled:opacity-25 transition ease-in-out duration-150">
                        {{ __('Edit Profile') }}
                    </a>
                    @elseif ($this->canInteract)
                    {{-- Follow/Unfollow/Pending Logic --}}
                    @if ($this->isFollowing)
                    <button wire:click="unfollow" wire:loading.attr="disabled"
                        class="inline-flex items-center px-4 py-2 bg-white dark:bg-neutral-700 border border-gray-300 dark:border-neutral-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-neutral-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-neutral-800 disabled:opacity-25 transition ease-in-out duration-150">
                        <span wire:loading wire:target="unfollow" class="mr-2">
                            <x-loading-spinner size="4" />
                        </span>
                        {{ __('Following') }}
                    </button>
                    @elseif ($this->hasSentFollowRequest)
                    <button wire:click="cancelFollowRequest" wire:loading.attr="disabled"
                        class="inline-flex items-center px-4 py-2 bg-yellow-100 dark:bg-yellow-800 border border-yellow-300 dark:border-yellow-700 rounded-md font-semibold text-xs text-yellow-700 dark:text-yellow-200 uppercase tracking-widest shadow-sm hover:bg-yellow-200 dark:hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 dark:focus:ring-offset-neutral-800 disabled:opacity-25 transition ease-in-out duration-150">
                        <span wire:loading wire:target="cancelFollowRequest" class="mr-2">
                            <x-loading-spinner size="4" />
                        </span>
                        {{ __('Request Sent') }}
                    </button>
                    @elseif ($this->hasPendingFollowRequestFrom)
                    {{-- Button to accept request FROM this user (Better on dedicated page) --}}
                    <span
                        class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                        {{ __('Wants to follow you') }}
                    </span>
                    @else
                    {{-- Follow Button --}}
                    <button wire:click="follow" wire:loading.attr="disabled"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-neutral-800 disabled:opacity-25 transition ease-in-out duration-150">
                        <span wire:loading wire:target="follow" class="mr-2">
                            <x-loading-spinner size="4" />
                        </span>
                        {{ $user->isPrivate() ? __('Request Follow') : __('Follow') }}
                    </button>
                    @endif
                    @endif
                    {{-- Add Write Message Button later if needed --}}
                    <a wire:navigate href="{{ route('mail.compose', [
                            'receiverId' => $user->id, 
                            'fixReceiver' => true
                            ]) }}"
                        class="ml-2 inline-flex items-center px-4 py-2 bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-700 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                        {{ __('Write a message') }}
                    </a>
                </div>

            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                {{-- Existing profile fields --}}
                <div>
                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Name') }}</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->name }}</p>
                </div>

                @if ($user->additionalInfo?->username)
                <div>
                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Username')
                        }}</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->additionalInfo->username }}</p>
                </div>
                @endif

                <div>
                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Email') }}</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->email }}</p>
                    {{-- Email verification status --}}
                    @if ($this->isOwnProfile) {{-- Only show verification status on own profile --}}
                    @if (!$user->hasVerifiedEmail())
                    <p class="mt-1 text-xs text-yellow-600 dark:text-yellow-400">
                        {{ __('Your email address is not verified.') }}
                        {{-- Add link to resend verification --}}
                    </p>
                    @else
                    <p class="mt-1 text-xs text-green-600 dark:text-green-400 flex items-center">
                        <flux:icon.check-circle class="size-8" /> {{ __('Email Verified') }}
                    </p>
                    @endif
                    @endif
                </div>

                @if ($user->additionalInfo?->birthday)
                <div>
                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Age') }}</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ \Carbon\Carbon::parse($user->additionalInfo->birthday)->age }}
                        {{ __('years old') }}
                    </p>
                </div>
                @endif

                @if ($user->additionalInfo?->nationality)
                <div>
                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Nationality')
                        }}</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->additionalInfo->nationality }}
                    </p>
                </div>
                @endif

                {{-- About Me --}}
                <div class="md:col-span-2">
                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('About Me')
                        }}</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{
                        $user->additionalInfo?->about_me ?: __('Not set') }}</p>
                </div>

            </div>
            {{-- Remove the old edit/message button section at the bottom --}}
        </div>
    </div>
</div>

{{-- You might need a loading spinner component, e.g., resources/views/components/loading-spinner.blade.php --}}
{{--
<x-loading-spinner /> --}}