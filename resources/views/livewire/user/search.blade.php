<div>
    <x-slot:title>
        {{ __('User Directory') }}
    </x-slot>

    <div class="space-y-6">
        {{-- Search Card replaced with div and Tailwind --}}
        <div class="p-4 sm:p-6 bg-white dark:bg-gray-800 rounded-lg shadow">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Search Users') }}</h2>
            <input type="text"
                   wire:model.live.debounce.300ms="search"
                   placeholder="{{ __('Search by username...') }}"
                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:border-indigo-600 dark:focus:ring-indigo-600 sm:text-sm">
        </div>

        <livewire:user.user-filters />

        @auth
            @if(strlen($search) >= 3 || $users->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @forelse ($users as $userToList) {{-- Renamed variable to avoid conflict if $user is outer-scoped --}}
                        <x-user-card :user="$userToList" />
                    @empty
                        @if(strlen($search) >= 3)
                            <div class="col-span-full text-center text-gray-500 dark:text-gray-400 py-10">
                                {{-- Basic icon replacement idea - consider SVG or font icon --}}
                                <svg class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <p class="mt-2 text-sm">{{ __('No users found matching your search criteria.') }}</p>
                            </div>
                        @endif
                    @endforelse
                </div>

                @if($users->hasPages())
                    <div class="mt-6">
                        {{ $users->links() }}
                    </div>
                @endif
            @elseif(strlen($search) > 0 && strlen($search) < 3)
                <div class="col-span-full text-center text-gray-500 dark:text-gray-400 py-10">
                    <p class="mt-2 text-sm">{{ __('Please enter at least 3 characters to search.') }}</p>
                </div>
            @endif 
            @else
            {{-- Message for Guests --}}
            <div class="mt-8 text-center bg-blue-100 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 text-blue-700 dark:text-blue-200 px-4 py-3 rounded relative"
                role="alert">
                <strong class="font-bold">{{ __('Login Required!') }}</strong>
                <span class="block sm:inline"> {{ __('Please') }} <a href="{{ route('login') }}"
                        class="font-semibold underline hover:text-blue-800 dark:hover:text-blue-100" wire:navigate>{{ __('log
                        in') }}</a> {{ __('or') }} <a href="{{ route('register') }}"
                        class="font-semibold underline hover:text-blue-800 dark:hover:text-blue-100" wire:navigate>{{
                        __('register') }}</a> {{ __('to see the search results.') }}</span>
            </div>           
        @endauth
    </div>
</div>