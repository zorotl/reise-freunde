<div class="max-w-3xl mx-auto py-8 px-4">
    <h2 class="text-2xl font-bold mb-4">{{ __('Notifications') }}</h2>

    @if ($notifications->isEmpty())
        <p class="text-gray-500">{{ __('You have no notifications.') }}</p>
    @else
        <button wire:click="markAllRead"
            class="mb-4 text-sm text-blue-600 hover:underline">{{ __('Mark all as read') }}</button>

        <div class="space-y-4">
            @foreach ($notifications as $notification)
                <div class="p-4 bg-white dark:bg-neutral-800 shadow rounded">
                    <h3 class="font-semibold">{{ $notification->data['title'] }}</h3>
                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $notification->data['body'] }}</p>
                    <a href="{{ $notification->data['url'] }}"
                        class="text-blue-500 text-sm hover:underline">{{ __('View') }}</a>
                </div>
            @endforeach
        </div>
    @endif
</div>
