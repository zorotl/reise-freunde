<div class="py-8">
    <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100 mb-6">
        {{ __('Follow Requests') }}
    </h1>

    {{-- Tab Switcher --}}
    @include('components.user-tab-switcher')

    {{-- Flash Messages --}}
    @if (session()->has('message'))
    <div
        class="mb-4 p-4 rounded-lg bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 border border-green-300 dark:border-green-700">
        {{ session('message') }}
    </div>
    @endif
    @if (session()->has('error'))
    <div
        class="mb-4 p-4 rounded-lg bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 border border-red-300 dark:border-red-700">
        {{ session('error') }}
    </div>
    @endif

    @if ($requests->count())
    <div
        class="bg-white dark:bg-neutral-800 shadow sm:rounded-lg overflow-hidden divide-y divide-gray-200 dark:divide-neutral-700">
        @foreach ($requests as $user)
        <div class="p-4 flex items-center justify-between gap-4">
            {{-- User Info --}}
            <x-user-card :user="$user" />

            {{-- Action Buttons --}}
            <div class="flex gap-2 flex-shrink-0">
                <button wire:click="acceptRequest({{ $user->id }})" wire:loading.attr="disabled"
                    wire:target="acceptRequest({{ $user->id }})"
                    class="inline-flex items-center px-3 py-1 border border-transparent shadow-sm text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50">
                    <span wire:loading wire:target="acceptRequest({{ $user->id }})" class="mr-1 -ml-1">
                        <flux:icon.loading />
                    </span>
                    {{ __('Accept') }}
                </button>

                <button wire:click="declineRequest({{ $user->id }})" wire:loading.attr="disabled"
                    wire:target="declineRequest({{ $user->id }})"
                    class="inline-flex items-center px-3 py-1 border border-gray-300 dark:border-neutral-600 shadow-sm text-xs font-medium rounded text-gray-700 dark:text-gray-300 bg-white dark:bg-neutral-700 hover:bg-gray-50 dark:hover:bg-neutral-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                    <span wire:loading wire:target="declineRequest({{ $user->id }})" class="mr-1 -ml-1">
                        <flux:icon.loading />
                    </span>
                    {{ __('Decline') }}
                </button>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $requests->links() }}
    </div>
    @else
    <div class="text-center py-10 text-gray-500 dark:text-gray-400">
        {{ __('You have no pending follow requests.') }}
    </div>
    @endif
</div>