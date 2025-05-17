{{-- resources/views/livewire/notification-section.blade.php --}}
{{-- This component displays pending follow requests. --}}
<section class="bg-white dark:bg-neutral-800 shadow sm:rounded-lg p-6">
    @php
        $notifications = auth()->user()->unreadNotifications()->limit(5)->get();
    @endphp

    @if ($notifications->isEmpty())
        <div class="text-sm text-gray-500 px-4 py-2">
            {{ __('No new notifications.') }}
        </div>
    @else
        @foreach ($notifications as $notification)
            <div class="px-4 py-2 border-b border-gray-100 dark:border-neutral-700 text-sm">
                <div class="font-semibold text-gray-800 dark:text-gray-200">
                    {{ $notification->data['title'] ?? 'Notification' }}
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
        @endforeach
        <div class="px-4 py-2">
            <a href="{{ route('notifications') }}" class="text-sm text-blue-500 hover:underline">
                {{ __('See all notifications') }}
            </a>
        </div>
    @endif

    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-200 mb-4">{{ __('Notifications') }}</h2>
    @if($pendingRequests->count() > 0)
    <ul class="space-y-3">
        @foreach($pendingRequests as $requestUser)
        <li wire:key="request-{{ $requestUser->id }}" class="flex items-center justify-between text-sm">
            <div class="flex items-center gap-2">
                {{-- User Avatar Placeholder --}}
                <span class="inline-block h-6 w-6 rounded-full overflow-hidden bg-gray-200 dark:bg-neutral-700">
                    <span
                        class="flex h-full w-full items-center justify-center font-medium text-gray-600 dark:text-gray-300 text-xs">
                        <img class="h-full w-full rounded-lg object-cover" src="{{ $requestUser->profilePictureUrl() }}"
                            alt="{{ $requestUser->additionalInfo?->username ?? 'profile_picture' }}" />
                    </span>
                </span>
                <a href="{{ route('user.profile', $requestUser->id) }}"
                    class="font-medium text-indigo-600 hover:underline dark:text-indigo-400" wire:navigate>
                    {{ $requestUser->name }}
                </a>
                <span class="text-gray-600 dark:text-gray-400">wants to follow you.</span>
            </div>
            {{-- Link to the full requests page --}}
            <a href="{{ route('user.follow-requests') }}"
                class="text-xs text-indigo-600 hover:underline dark:text-indigo-400" wire:navigate>
                View
            </a>
        </li>
        @endforeach
        {{-- Optionally add a link to view all requests if there are more than displayed --}}
    </ul>
    @else
    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No new notifications.') }}</p>
    @endif
</section>