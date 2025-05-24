<div>
    <x-slot:title>
        {{ __('Followers of :name', ['name' => $user->username]) }}
    </x-slot>

    <x-user-tab-switcher :user="$user" /> {{-- Assuming this is your own component or standard HTML/Tailwind --}}

    <div class="mt-6">
        {{-- Card replaced with div and Tailwind --}}
        <div class="p-4 sm:p-6 bg-white dark:bg-gray-800 rounded-lg shadow">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Followers') }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @forelse ($followers as $follower)
                    <x-user-card :user="$follower" />
                @empty
                     <div class="col-span-full text-center text-gray-500 dark:text-gray-400 py-10">
                        <svg class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <p class="mt-2 text-sm">{{ __(':name has no followers yet.', ['name' => $user->username]) }}</p>
                    </div>
                @endforelse
            </div>
            @if($followers->hasPages())
                <div class="mt-6">
                    {{ $followers->links() }}
                </div>
            @endif
        </div>
    </div>
</div>