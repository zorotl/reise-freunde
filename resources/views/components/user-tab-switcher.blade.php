<div class="mb-6 flex flex-wrap gap-4 border-b border-gray-200 dark:border-neutral-600">
    <a href="{{ route('user.followers', auth()->id()) }}" @class([ 'pb-2 font-medium transition' ,
        request()->routeIs('user.followers')
        ? 'text-indigo-600 border-b-2 border-indigo-600'
        : 'text-gray-600 hover:text-indigo-500'
        ])
        wire:navigate>
        {{ __('Followers') }}
    </a>

    <a href="{{ route('user.following', auth()->id()) }}" @class([ 'pb-2 font-medium transition' ,
        request()->routeIs('user.following')
        ? 'text-indigo-600 border-b-2 border-indigo-600'
        : 'text-gray-600 hover:text-indigo-500'
        ])
        wire:navigate>
        {{ __('Following') }}
    </a>

    <a href="{{ route('user.follow-requests') }}" @class([ 'pb-2 font-medium transition' ,
        request()->routeIs('user.follow-requests')
        ? 'text-indigo-600 border-b-2 border-indigo-600'
        : 'text-gray-600 hover:text-indigo-500'
        ])
        wire:navigate>
        {{ __('Follow Requests') }}
    </a>
</div>