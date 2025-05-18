<div class="mb-6 flex flex-wrap gap-4 border-b border-gray-200 dark:border-neutral-600">
    <a href="{{ route('mail.inbox') }}" @class([ 'pb-2 font-medium transition' , request()->routeIs('mail.inbox')
        ? 'text-indigo-600 border-b-2 border-indigo-600 dark:text-indigo-400 dark:border-indigo-400'
        : 'text-gray-600 hover:text-indigo-500 dark:text-gray-300 dark:hover:text-indigo-400'
        ])
        wire:navigate>
        {{ __('Inbox') }}
    </a>

    <a href="{{ route('mail.outbox') }}" @class([ 'pb-2 font-medium transition' , request()->routeIs('mail.outbox')
        ? 'text-indigo-600 border-b-2 border-indigo-600 dark:text-indigo-400 dark:border-indigo-400'
        : 'text-gray-600 hover:text-indigo-500 dark:text-gray-300 dark:hover:text-indigo-400'
        ])
        wire:navigate>
        {{ __('Outbox') }}
    </a>

    <a href="{{ route('mail.archive') }}" @class([ 'pb-2 font-medium transition' , request()->routeIs('mail.archive')
        ? 'text-indigo-600 border-b-2 border-indigo-600 dark:text-indigo-400 dark:border-indigo-400'
        : 'text-gray-600 hover:text-indigo-500 dark:text-gray-300 dark:hover:text-indigo-400'
        ])
        wire:navigate>
        {{ __('Archive') }}
    </a>

    <a href="{{ route('mail.trash') }}" @class([ 'pb-2 font-medium transition' , request()->routeIs('mail.trash')
        ? 'text-red-600 border-b-2 border-red-600 dark:text-red-400 dark:border-red-400'  // Different color for trash maybe
        : 'text-gray-600 hover:text-red-500 dark:text-gray-300 dark:hover:text-red-400'
        ])
        wire:navigate>
        {{ __('Trash') }}
    </a>

    <a href="{{ route('mail.compose') }}" @class([ 'pb-2 font-medium transition' , request()->routeIs('mail.compose')
        ? 'text-indigo-600 border-b-2 border-indigo-600 dark:text-indigo-400 dark:border-indigo-400'
        : 'text-gray-600 hover:text-indigo-500 dark:text-gray-300 dark:hover:text-indigo-400'
        ])
        wire:navigate>
        {{ __('Compose') }}
    </a>
</div>