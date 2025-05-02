<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-neutral-800 shadow sm:rounded-lg overflow-hidden">
            <div
                class="px-4 py-5 sm:px-6 bg-gray-50 dark:bg-neutral-900 border-b border-gray-200 dark:border-neutral-700 flex items-center justify-between flex-wrap gap-4">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                        {{ __('Followers of :name', ['name' => $user->firstname]) }}
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
                        <a href="{{ route('user.profile', $user->id) }}" wire:navigate class="hover:underline">&larr; {{
                            __('Back to profile') }}</a>
                    </p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('user.following', $user->id) }}" wire:navigate
                        class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-neutral-600 shadow-sm text-xs font-medium rounded text-gray-700 dark:text-gray-300 bg-white dark:bg-neutral-700 hover:bg-gray-50 dark:hover:bg-neutral-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                        {{ __('Following') }}
                    </a>
                    @if (auth()->check() && auth()->id() === $user->id)
                    <a href="{{ route('user.follow-requests') }}" wire:navigate
                        class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-neutral-600 shadow-sm text-xs font-medium rounded text-gray-700 dark:text-gray-300 bg-white dark:bg-neutral-700 hover:bg-gray-50 dark:hover:bg-neutral-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                        {{ __('Follow Requests') }}
                    </a>
                    @endif
                </div>
            </div>
            <div class="border-t border-gray-200 dark:border-neutral-700">
                @if ($followers->count() > 0)
                <ul role="list" class="divide-y divide-gray-200 dark:divide-neutral-700">
                    @foreach ($followers as $follower)
                    <li wire:key="{{ $follower->id }}"
                        class="px-4 py-4 sm:px-6 flex items-center justify-between gap-4 hover:bg-gray-50 dark:hover:bg-neutral-750">
                        <div class="flex items-center gap-4">
                            {{-- Avatar Placeholder --}}
                            <div
                                class="rounded-full overflow-hidden w-10 h-10 bg-gray-300 flex items-center justify-center flex-shrink-0">
                                <span class="text-sm text-gray-600 dark:text-gray-400">
                                    <img class="h-full w-full rounded-lg object-cover"
                                        src="{{ auth()->user()->profilePictureUrl() }}"
                                        alt="{{ auth()->user()->additionalInfo->username }}" />
                                </span>
                            </div>
                            <div>
                                <a href="{{ route('user.profile', $follower->id) }}" wire:navigate
                                    class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 truncate">
                                    {{ $follower->name }}
                                </a>
                                @if($follower->additionalInfo?->username)
                                <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ '@' .
                                    $follower->additionalInfo->username }}</p>
                                @endif
                            </div>
                        </div>
                        {{-- Optional: Add Remove Follower button if applicable --}}
                        {{-- <div>
                            <button class="...">Remove</button>
                        </div> --}}
                    </li>
                    @endforeach
                </ul>
                <div
                    class="px-4 py-3 sm:px-6 bg-gray-50 dark:bg-neutral-900 border-t border-gray-200 dark:border-neutral-700">
                    {{ $followers->links() }}
                </div>
                @else
                <p class="text-center text-gray-500 dark:text-gray-400 py-10">
                    {{ __('This user has no followers yet.') }}
                </p>
                @endif
            </div>
        </div>
    </div>
</div>