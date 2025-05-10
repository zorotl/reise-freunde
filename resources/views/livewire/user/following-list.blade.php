<div class="py-8">
    <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100 mb-6">
        {{ __('Following') }} {{-- Adjust for each page --}}
    </h1>

    {{-- Tab Switcher --}}
    @include('components.user-tab-switcher') {{-- Optional extract if reused --}}

    {{-- User List --}}
    @if ($following->count())
    <div class="bg-white dark:bg-neutral-800 shadow sm:rounded-lg overflow-hidden">
        @foreach ($following as $user)
        <x-user-card :user="$user" :show-actions="true" />
        @endforeach
    </div>

    <div class="mt-4">
        {{ $following->links() }}
    </div>
    @else
    <div class="text-center py-10 text-gray-500 dark:text-gray-400">
        {{ __('No followings found.') }}
    </div>
    @endif
</div>