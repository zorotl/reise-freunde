<div class="mb-6 border-b border-gray-200 dark:border-neutral-600">
    <div class="flex flex-wrap items-center gap-2 sm:gap-4">
        {{-- Inbox --}}
        <a href="{{ route('mail.inbox') }}"
            class="px-3 py-2 text-sm font-medium transition border-b-2 
                {{ request()->routeIs('mail.inbox') 
                    ? 'text-indigo-600 border-indigo-600 dark:text-indigo-400 dark:border-indigo-400'
                    : 'text-gray-600 border-transparent hover:text-indigo-500 dark:text-gray-300 dark:hover:text-indigo-400'
                }}"
            wire:navigate>
            {{ __('Inbox') }}
        </a>

        {{-- Outbox --}}
        <a href="{{ route('mail.outbox') }}"
            class="px-3 py-2 text-sm font-medium transition border-b-2 
                {{ request()->routeIs('mail.outbox') 
                    ? 'text-indigo-600 border-indigo-600 dark:text-indigo-400 dark:border-indigo-400'
                    : 'text-gray-600 border-transparent hover:text-indigo-500 dark:text-gray-300 dark:hover:text-indigo-400'
                }}"
            wire:navigate>
            {{ __('Outbox') }}
        </a>

        {{-- Archive --}}
        <a href="{{ route('mail.archive') }}"
            class="px-3 py-2 text-sm font-medium transition border-b-2 
                {{ request()->routeIs('mail.archive') 
                    ? 'text-indigo-600 border-indigo-600 dark:text-indigo-400 dark:border-indigo-400'
                    : 'text-gray-600 border-transparent hover:text-indigo-500 dark:text-gray-300 dark:hover:text-indigo-400'
                }}"
            wire:navigate>
            {{ __('Archive') }}
        </a>

        {{-- Trash --}}
        <a href="{{ route('mail.trash') }}"
            class="px-3 py-2 text-sm font-medium transition border-b-2 
                {{ request()->routeIs('mail.trash') 
                    ? 'text-indigo-600 border-indigo-600 dark:text-indigo-400 dark:border-indigo-400'
                    : 'text-gray-600 border-transparent hover:text-indigo-500 dark:text-gray-300 dark:hover:text-indigo-400'
                }}"
            wire:navigate>
            {{ __('Trash') }}
        </a>

        {{-- Compose (CTA-Style) --}}
        <a href="{{ route('mail.compose') }}"
            class="ml-auto inline-flex items-center px-4 py-2 rounded-md text-sm font-semibold shadow-sm transition 
                {{ request()->routeIs('mail.compose') 
                    ? 'text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-600'
                    : 'text-white bg-indigo-500 hover:bg-indigo-600 focus:ring-indigo-500 dark:bg-indigo-600 dark:hover:bg-indigo-700'
                }}"
            wire:navigate>
            <flux:icon.pencil class="h-4 w-4 mr-2" />
            {{ __('Compose') }}
        </a>
    </div>
</div>
