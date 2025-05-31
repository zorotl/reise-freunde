{{-- resources/views/livewire/notification-section.blade.php --}}
<section class="bg-white dark:bg-neutral-800 shadow sm:rounded-lg p-6 space-y-6">
    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-200">
        {{ __('Notifications') }}
    </h2>

    {{-- Allgemeine Notifications --}}
    @php
        $notifications = auth()->user()->unreadNotifications()->limit(5)->get();
    @endphp

    @if ($notifications->isEmpty())
        <p class="text-sm text-gray-500 dark:text-gray-400">
            {{ __('No new notifications.') }}
        </p>
    @else
        <ul class="space-y-4">
            @foreach ($notifications as $notification)
                <li class="border-b border-gray-100 dark:border-neutral-700 pb-2">
                    <div class="text-sm">
                        <div class="font-semibold text-gray-800 dark:text-gray-200">
                            {{ $notification->data['title'] ?? __('Notification') }}
                        </div>
                        <div class="text-gray-600 dark:text-gray-400">
                            {{ $notification->data['body'] ?? '' }}
                        </div>
                        @if (!empty($notification->data['url']))
                            <a href="{{ $notification->data['url'] }}"
                               class="text-blue-500 text-sm hover:underline">
                                {{ __('View') }}
                            </a>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>

        <div>
            <a href="{{ route('notifications') }}" class="text-sm text-blue-500 hover:underline">
                {{ __('See all notifications') }}
            </a>
        </div>
    @endif

    {{-- Follow Requests --}}
    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mt-6">
        {{ __('Follow Requests') }}
    </h3>
    @if($pendingRequests->count() > 0)        
        <ul class="space-y-3">
            @foreach($pendingRequests as $requestUser)
                <li wire:key="request-{{ $requestUser->id }}" class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2">
                        <span class="inline-block h-6 w-6 rounded-full overflow-hidden bg-gray-200 dark:bg-neutral-700">
                            <img class="h-full w-full object-cover" src="{{ $requestUser->profilePictureUrl() }}"
                                 alt="{{ $requestUser->additionalInfo?->username ?? 'profile_picture' }}" />
                        </span>
                        <a href="{{ route('user.profile', $requestUser->id) }}"
                           class="font-medium text-indigo-600 hover:underline dark:text-indigo-400" wire:navigate>
                            {{ $requestUser->name }}
                        </a>
                        <span class="text-gray-600 dark:text-gray-400">{{ __('wants to follow you.') }}</span>
                    </div>
                    <a href="{{ route('user.follow-requests') }}"
                       class="text-xs text-indigo-600 hover:underline dark:text-indigo-400" wire:navigate>
                        {{ __('View') }}
                    </a>
                </li>
            @endforeach
        </ul>
    @endif

    @if($notifications->isEmpty() && $pendingRequests->isEmpty())
        <p class="text-sm text-gray-500 dark:text-gray-400">
            {{ __('You have no new notifications or requests.') }}
        </p>
    @endif
</section>
