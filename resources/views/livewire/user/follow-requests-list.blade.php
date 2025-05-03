<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-neutral-800 shadow sm:rounded-lg overflow-hidden">
            <div
                class="px-4 py-5 sm:px-6 bg-gray-50 dark:bg-neutral-900 border-b border-gray-200 dark:border-neutral-700 flex items-center justify-between flex-wrap gap-4">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                        {{ __('Pending Follow Requests') }}
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
                        {{ __('Users who want to follow you.') }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('user.following', $loggedInUser->id ?? auth()->id()) }}" wire:navigate
                        class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-neutral-600 shadow-sm text-xs font-medium rounded text-gray-700 dark:text-gray-300 bg-white dark:bg-neutral-700 hover:bg-gray-50 dark:hover:bg-neutral-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                        {{ __('Following') }}
                    </a>
                    <a href="{{ route('user.followers', $loggedInUser->id ?? auth()->id()) }}" wire:navigate
                        class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-neutral-600 shadow-sm text-xs font-medium rounded text-gray-700 dark:text-gray-300 bg-white dark:bg-neutral-700 hover:bg-gray-50 dark:hover:bg-neutral-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                        {{ __('Followers') }}
                    </a>
                </div>
            </div>

            {{-- Flash Messages --}}
            @if (session()->has('message'))
            <div class="px-4 py-3 border-b border-gray-200 dark:border-neutral-700 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-200"
                role="alert">
                {{ session('message') }}
            </div>
            @endif
            @if (session()->has('error'))
            <div class="px-4 py-3 border-b border-gray-200 dark:border-neutral-700 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-200"
                role="alert">
                {{ session('error') }}
            </div>
            @endif


            <div class="border-t border-gray-200 dark:border-neutral-700">
                @if ($requests->count() > 0)
                <ul role="list" class="divide-y divide-gray-200 dark:divide-neutral-700">
                    @foreach ($requests as $requester)
                    <li wire:key="{{ $requester->id }}"
                        class="px-4 py-4 sm:px-6 flex items-center justify-between gap-4 hover:bg-gray-50 dark:hover:bg-neutral-750">
                        {{-- Requester Info --}}
                        <div class="flex items-center gap-4">
                            <div
                                class="rounded-full overflow-hidden w-10 h-10 bg-gray-300 flex items-center justify-center flex-shrink-0">
                                <span class="text-sm text-gray-600 dark:text-gray-400">
                                    <img class="h-full w-full rounded-lg object-cover"
                                        src="{{ $requester->profilePictureUrl() }}"
                                        alt="{{ $requester->additionalInfo?->username }}" />
                                </span>
                            </div>
                            <div>
                                <a href="{{ route('user.profile', $requester->id) }}" wire:navigate
                                    class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 truncate">
                                    {{ $requester->additionalInfo?->username}}
                                </a>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex gap-2 flex-shrink-0">
                            <button wire:click="acceptRequest({{ $requester->id }})" wire:loading.attr="disabled"
                                wire:target="acceptRequest({{ $requester->id }})"
                                class="inline-flex items-center px-3 py-1 border border-transparent shadow-sm text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50">
                                <span wire:loading wire:target="acceptRequest({{ $requester->id }})" class="mr-1 -ml-1">
                                    <flux:icon.loading />
                                </span>
                                {{ __('Accept') }}
                            </button>
                            <button wire:click="declineRequest({{ $requester->id }})" wire:loading.attr="disabled"
                                wire:target="declineRequest({{ $requester->id }})"
                                class="inline-flex items-center px-3 py-1 border border-gray-300 dark:border-neutral-600 shadow-sm text-xs font-medium rounded text-gray-700 dark:text-gray-300 bg-white dark:bg-neutral-700 hover:bg-gray-50 dark:hover:bg-neutral-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                                <span wire:loading wire:target="declineRequest({{ $requester->id }})"
                                    class="mr-1 -ml-1">
                                    <flux:icon.loading />
                                </span>
                                {{ __('Decline') }}
                            </button>
                        </div>
                    </li>
                    @endforeach
                </ul>
                <div
                    class="px-4 py-3 sm:px-6 bg-gray-50 dark:bg-neutral-900 border-t border-gray-200 dark:border-neutral-700">
                    {{ $requests->links() }}
                </div>
                @else
                <p class="text-center text-gray-500 dark:text-gray-400 py-10">
                    {{ __('You have no pending follow requests.') }}
                </p>
                @endif
            </div>
        </div>
    </div>
</div>