<div>
    {{-- Main content area --}}
    <div class="py-8 w-full">

        {{-- Welcome Message (can remain here) --}}
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100 mb-6">
            {{ __('Welcome back, :name!', ['name' => $user->firstname]) }}
        </h1>

        {{-- Status message for follow actions (can remain here) --}}
        @if (session()->has('status'))
        <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-900 dark:text-green-300"
            role="alert">
            {{ session('status') }}
        </div>
        @endif

        {{-- Grid for Dashboard Sections --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Main Content Area (Feed) - Spans 2 columns on large screens --}}
            <div class="lg:col-span-2 space-y-6">
                <livewire:parts.feed-section :feedPosts="$feedPosts" :show="$show" />
            </div>

            {{-- Sidebar Area (Notifications, Suggestions, Counts) - Spans 1 column on large screens --}}
            <div class="lg:col-span-1 space-y-6">
                {{-- Pass the pendingRequests and user data to the NotificationSection component --}}
                <livewire:parts.notification-section :pendingRequests="$pendingRequests" :user="$user" />

                {{-- Pass the suggestedUsers data to the SuggestedUsersSection component --}}
                <livewire:parts.suggested-users-section :suggestedUsers="$suggestedUsers" />

                {{-- Pass the follower/following counts and user data to the NetworkStats component --}}
                <livewire:parts.network-stats :followerCount="$followerCount" :followingCount="$followingCount"
                    :user="$user" />
            </div>
        </div>
    </div>
</div>