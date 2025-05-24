@props(['user'])

<div class="flex flex-col p-4 bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700">
    <div class="flex-grow space-y-4">
        <a href="{{ route('user.profile', $user->id) }}" class="flex flex-col items-center text-center">
            <span class="relative flex h-16 w-16 shrink-0 overflow-hidden rounded-lg">
                <img class="h-full w-full rounded-lg object-cover"
                     src="{{ $user->avatar_url ?? asset('images/default-avatar.png') }}"
                     alt="{{ $user->additionalInfo?->username ?? $user->first_name ?? 'profile_picture' }}" />
            </span>
            <h3 class="mt-2 text-base font-semibold text-gray-900 dark:text-gray-100">{{ $user->additionalInfo?->username ?? $user->first_name }}</h3>
        </a>
        <div class="flex justify-around text-center text-xs text-gray-500 dark:text-gray-400">
            <div>
                <span class="block font-bold text-sm text-gray-700 dark:text-gray-200">{{ $user->followers_count ?? $user->followers()->count() }}</span>
                <span>{{ __('Followers') }}</span>
            </div>
            <div>
                <span class="block font-bold text-sm text-gray-700 dark:text-gray-200">{{ $user->following_count ?? $user->following()->count() }}</span>
                <span>{{ __('Following') }}</span>
            </div>
        </div>
    </div>

    @auth
    @if(auth()->id() !== $user->id)
        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            @if(auth()->user()->isFollowing($user))
                {{-- FOLLOWING: Confirm unfollow --}}
                <div x-data="{ confirm: false }">
                    <template x-if="!confirm">
                        <button type="button" @click="confirm = true"
                            class="w-full inline-flex justify-center items-center px-3 py-2 text-xs font-medium rounded-md shadow-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-neutral-700 border border-gray-300 dark:border-neutral-600 hover:bg-gray-50 dark:hover:bg-neutral-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-neutral-800 transition ease-in-out duration-150">
                            <span class="mr-2">
                                <flux:icon.check />
                            </span>
                            {{ __('Following') }}
                        </button>
                    </template>
                    <template x-if="confirm">
                        <div class="flex space-x-2">
                            <button type="button" @click="confirm = false"
                                class="w-full inline-flex justify-center items-center px-3 py-2 text-xs font-medium rounded-md shadow-sm text-gray-500 dark:text-gray-300 bg-gray-100 dark:bg-neutral-800 border border-gray-300 dark:border-neutral-600 hover:bg-gray-200 dark:hover:bg-neutral-700 transition">
                                {{ __('Cancel') }}
                            </button>
                            <button type="button" wire:click="unfollowUser({{ $user->id }})" wire:loading.attr="disabled"
                                class="w-full inline-flex justify-center items-center px-3 py-2 text-xs font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-neutral-800 transition">
                                <span wire:loading wire:target="unfollowUser" class="mr-2">
                                    <flux:icon.loading />
                                </span>
                                {{ __('Unfollow') }}
                            </button>
                        </div>
                    </template>
                </div>

            @elseif(auth()->user()->hasSentFollowRequestTo($user))
                {{-- REQUEST SENT: Confirm cancel --}}
                <div x-data="{ confirm: false }">
                    <template x-if="!confirm">
                        <button type="button" @click="confirm = true"
                            class="w-full inline-flex justify-center items-center px-3 py-2 text-xs font-medium rounded-md text-yellow-700 dark:text-yellow-200 bg-yellow-100 dark:bg-yellow-800 border border-yellow-300 dark:border-yellow-700 hover:bg-yellow-200 dark:hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 dark:focus:ring-offset-neutral-800 transition ease-in-out duration-150">
                            <span class="mr-2">
                                <flux:icon.clock />
                            </span>
                            {{ __('Request Sent') }}
                        </button>
                    </template>
                    <template x-if="confirm">
                        <div class="flex space-x-2">
                            <button type="button" @click="confirm = false"
                                class="w-full inline-flex justify-center items-center px-3 py-2 text-xs font-medium rounded-md shadow-sm text-gray-500 dark:text-gray-300 bg-gray-100 dark:bg-neutral-800 border border-gray-300 dark:border-neutral-600 hover:bg-gray-200 dark:hover:bg-neutral-700 transition">
                                {{ __('Cancel') }}
                            </button>
                            <button type="button" wire:click="cancelFollowRequest({{ $user->id }})" wire:loading.attr="disabled"
                                class="w-full inline-flex justify-center items-center px-3 py-2 text-xs font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 dark:focus:ring-offset-neutral-800 transition ease-in-out duration-150">
                                <span wire:loading wire:target="cancelFollowRequest" class="mr-2">
                                    <flux:icon.loading />
                                </span>
                                {{ __('Cancel Request') }}
                            </button>
                        </div>
                    </template>
                </div>

            @else
                {{-- FOLLOW --}}
                <button type="button" wire:click="followUser({{ $user->id }})" wire:loading.attr="disabled"
                    class="w-full inline-flex justify-center items-center px-3 py-2 text-xs font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-neutral-800 disabled:opacity-25 transition ease-in-out duration-150">
                    
                    {{-- Default icon --}}
                    <span class="mr-2" wire:loading.remove wire:target="followUser">
                        <flux:icon.plus />
                    </span>

                    {{-- Loading icon --}}
                    <span class="mr-2" wire:loading wire:target="followUser">
                        <flux:icon.loading />
                    </span>

                    {{ $user->isPrivate() ? __('Request Follow') : __('Follow') }}
                </button>
            @endif
        </div>
    @endif
@endauth

</div>