<div class="mb-6 border-b border-gray-200 dark:border-neutral-600">
    <div 
        class="flex flex-nowrap gap-2 sm:gap-4 overflow-x-auto"
        x-data
        x-init="$el.classList.add('scrollbar-hide')"
    >
        {{-- Followers --}}
        <a href="{{ route('user.followers', auth()->id()) }}"
            class="shrink-0 px-3 py-2 text-sm font-medium transition border-b-2
                {{ request()->routeIs('user.followers') 
                    ? 'text-indigo-600 border-indigo-600 dark:text-indigo-400 dark:border-indigo-400'
                    : 'text-gray-600 border-transparent hover:text-indigo-500 dark:text-gray-300 dark:hover:text-indigo-400'
                }}"
            wire:navigate>
            {{ __('Followers') }}
        </a>

        {{-- Following --}}
        <a href="{{ route('user.following', auth()->id()) }}"
            class="shrink-0 px-3 py-2 text-sm font-medium transition border-b-2
                {{ request()->routeIs('user.following') 
                    ? 'text-indigo-600 border-indigo-600 dark:text-indigo-400 dark:border-indigo-400'
                    : 'text-gray-600 border-transparent hover:text-indigo-500 dark:text-gray-300 dark:hover:text-indigo-400'
                }}"
            wire:navigate>
            {{ __('Following') }}
        </a>

        {{-- Follow Requests --}}
        <a href="{{ route('user.follow-requests') }}"
            class="shrink-0 px-3 py-2 text-sm font-medium transition border-b-2
                {{ request()->routeIs('user.follow-requests') 
                    ? 'text-indigo-600 border-indigo-600 dark:text-indigo-400 dark:border-indigo-400'
                    : 'text-gray-600 border-transparent hover:text-indigo-500 dark:text-gray-300 dark:hover:text-indigo-400'
                }}"
            wire:navigate>
            {{ __('Follow Requests') }}
        </a>

        {{-- Real-World Confirmations --}}
        <a href="{{ route('profile.confirmations') }}"
            class="shrink-0 px-3 py-2 text-sm font-medium transition border-b-2
                {{ request()->routeIs('profile.confirmations') 
                    ? 'text-indigo-600 border-indigo-600 dark:text-indigo-400 dark:border-indigo-400'
                    : 'text-gray-600 border-transparent hover:text-indigo-500 dark:text-gray-300 dark:hover:text-indigo-400'
                }}"
            wire:navigate>
            {{ __('Real-World Requests') }}
        </a>
    </div>
</div>
