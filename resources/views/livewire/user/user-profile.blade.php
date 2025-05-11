<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="bg-white dark:bg-neutral-800 shadow sm:rounded-lg overflow-hidden">
            <div
                class="px-4 py-5 sm:px-6 bg-gray-50 dark:bg-neutral-900 border-b border-gray-200 dark:border-neutral-700 flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-4">
                    {{-- Profile Picture --}}
                    <div
                        class="rounded-full overflow-hidden w-16 h-16 sm:w-24 sm:h-24 bg-gray-300 flex items-center justify-center flex-shrink-0">
                        <span class="text-xl text-gray-600 dark:text-gray-400">
                            <img class="h-full w-full rounded-lg object-cover" src="{{ $user->profilePictureUrl() }}"
                                alt="{{ $user->additionalInfo->username }}" />
                        </span>
                    </div>
                    {{-- Name/Username & Counts --}}
                    <div>
                        {{-- Req 1.1: Show Name for own profile, Username otherwise --}}
                        <h2 class="text-lg sm:text-xl font-medium text-gray-900 dark:text-gray-100">
                            @if ($this->isOwnProfile)
                            {{ $user->name }}
                            @else
                            {{-- Show username if available, otherwise fall back to firstname (shouldn't happen often)
                            --}}
                            {{ $user->additionalInfo?->username ?: $user->firstname }}
                            @endif
                            <livewire:profile.badges :user="$user" />
                        </h2>
                        {{-- Show username below name only if it's NOT the main title and exists --}}
                        @if ($this->isOwnProfile && $user->additionalInfo?->username)
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ '@' . $user->additionalInfo->username }}
                        </p>
                        @endif

                        {{-- Show Private icon if applicable --}}
                        @if($user->isPrivate())
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 flex items-center">
                            <flux:icon.lock-closed class="size-4 mr-1" /> {{-- Adjusted icon size --}}
                            <span>{{ __('Private Account') }}</span>
                        </p>
                        @endif

                        {{-- Req 2: Conditionally show Follower/Following Counts --}}
                        @if ($this->canViewSensitiveInfo)
                        <div class="mt-2 flex space-x-4 text-sm text-gray-600 dark:text-gray-400">
                            <a href="{{ route('user.followers', $user->id) }}" wire:navigate class="hover:underline">
                                <span class="font-semibold text-gray-900 dark:text-gray-100">{{
                                    $this->user->followers_count }}</span> {{ __('Followers') }}
                            </a>
                            <a href="{{ route('user.following', $user->id) }}" wire:navigate class="hover:underline">
                                <span class="font-semibold text-gray-900 dark:text-gray-100">{{
                                    $this->user->following_count }}</span> {{ __('Following') }}
                            </a>
                        </div>
                        @else
                        {{-- Optionally show placeholder text for private profiles if not following --}}
                        <div class="mt-2 text-sm text-gray-500 dark:text-gray-400 italic">
                            {{ __('Follow this user to see their network.') }}
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Action Buttons (existing logic is fine) --}}
                <div class="flex-shrink-0">
                    @if ($this->isOwnProfile)
                    {{-- Edit Profile Button --}}
                    <a wire:navigate href="{{ route('settings.profile') }}"
                        class="inline-flex items-center px-4 py-2 bg-white dark:bg-neutral-700 border border-gray-300 dark:border-neutral-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-neutral-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-neutral-800 disabled:opacity-25 transition ease-in-out duration-150">
                        {{ __('Edit Profile') }}
                    </a>
                    @elseif ($this->canInteract)
                    {{-- Follow/Unfollow/Pending Logic --}}
                    @if ($this->isFollowing)
                    <button wire:click="unfollow" wire:loading.attr="disabled"
                        class="inline-flex items-center px-4 py-2 bg-white dark:bg-neutral-700 border border-gray-300 dark:border-neutral-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-neutral-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-neutral-800 disabled:opacity-25 transition ease-in-out duration-150">
                        <span wire:loading wire:target="unfollow" class="mr-2">
                            <flux:icon.loading />
                        </span>
                        {{ __('Following') }}
                    </button>
                    @elseif ($this->hasSentFollowRequest)
                    <button wire:click="cancelFollowRequest" wire:loading.attr="disabled"
                        class="inline-flex items-center px-4 py-2 bg-yellow-100 dark:bg-yellow-800 border border-yellow-300 dark:border-yellow-700 rounded-md font-semibold text-xs text-yellow-700 dark:text-yellow-200 uppercase tracking-widest shadow-sm hover:bg-yellow-200 dark:hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 dark:focus:ring-offset-neutral-800 disabled:opacity-25 transition ease-in-out duration-150">
                        <span wire:loading wire:target="cancelFollowRequest" class="mr-2">
                            <flux:icon.loading />
                        </span>
                        {{ __('Request Sent') }}
                    </button>
                    @elseif ($this->hasPendingFollowRequestFrom)
                    <span
                        class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                        {{ __('Wants to follow you') }}
                    </span>
                    {{-- Consider adding Accept/Decline here or keeping them separate --}}
                    @else
                    {{-- Follow Button --}}
                    <button wire:click="follow" wire:loading.attr="disabled"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-neutral-800 disabled:opacity-25 transition ease-in-out duration-150">
                        <span wire:loading wire:target="follow" class="mr-2">
                            <flux:icon.loading />
                        </span>
                        {{ $user->isPrivate() ? __('Request Follow') : __('Follow') }}
                    </button>
                    @endif
                    {{-- Message Button - Always show if can interact --}}
                    <a wire:navigate
                        href="{{ route('mail.compose', ['receiverId' => $user->id, 'fixReceiver' => true]) }}"
                        class="ml-2 inline-flex items-center px-4 py-2 bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-700 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                        {{ __('Write a message') }}
                    </a>
                    @endif
                </div>
            </div>

            {{-- Details Section --}}
            {{-- Req 2: Conditionally show details if profile is private --}}
            @if ($this->canViewSensitiveInfo)
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                {{-- Req 1.2: Only show Name div if own profile --}}
                @if ($this->isOwnProfile)
                <div>
                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Name') }}</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->name }}</p>
                </div>
                @endif

                {{-- Show username if available (removed redundant check, username is shown in header if not own
                profile) --}}
                @if ($user->additionalInfo?->username && $this->isOwnProfile) {{-- Only show again if own profile and it
                exists --}}
                <div>
                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Username')
                        }}</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->additionalInfo->username }}</p>
                </div>
                @endif

                {{-- Req 1.2: Only show Email div if own profile --}}
                @if ($this->isOwnProfile)
                <div>
                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Email') }}</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $user->email }}</p>
                    {{-- Email verification status --}}
                    @if (!$user->hasVerifiedEmail())
                    <p class="mt-1 text-xs text-yellow-600 dark:text-yellow-400">
                        {{ __('Your email address is not verified.') }}
                        {{-- Add link to resend verification if needed --}}
                    </p>
                    @else
                    <p class="mt-1 text-xs text-green-600 dark:text-green-400 flex items-center">
                        <flux:icon.check-circle class="size-4 mr-1" /> {{ __('Email Verified') }}
                    </p>
                    @endif
                </div>
                @endif

                {{-- Other details (assuming they follow same privacy rule) --}}
                @if ($user->additionalInfo?->birthday)
                <div>
                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Age') }}</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ \Carbon\Carbon::parse($user->additionalInfo?->birthday)->age }}
                        {{ __('years old') }}
                    </p>
                </div>
                @endif

                @if ($user->additionalInfo?->nationality)
                <div>
                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Nationality')
                        }}</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{-- Access the component property directly using $this --}}
                        {{ $this->countryList[$user->additionalInfo->nationality] ?? $user->additionalInfo->nationality
                        }}
                    </p>
                </div>
                @endif

                <div class="md:col-span-2">
                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('About Me')
                        }}</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{
                        $user->additionalInfo?->about_me ?: __('Not set') }}</p>
                </div>

                {{-- Travel Styles --}}
                @if ($user->travelStyles->count() || ($user->additionalInfo?->custom_travel_styles &&
                count($user->additionalInfo->custom_travel_styles) > 0) )
                <div class="md:col-span-2">
                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Travel Styles</label>
                    <div class="mt-1 flex flex-wrap gap-2">
                        @foreach ($user->travelStyles as $style)
                        <span
                            class="px-3 py-1 rounded-full text-sm bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                            {{ $style->name }}
                        </span>
                        @endforeach
                        @if ($user->additionalInfo?->custom_travel_styles)
                        @foreach ($user->additionalInfo->custom_travel_styles as $style)
                        <span
                            class="px-3 py-1 rounded-full text-sm bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                            {{ $style }}
                        </span>
                        @endforeach
                        @endif
                    </div>
                </div>
                @endif

                {{-- Hobbies --}}
                @if ($user->hobbies->count() || ($user->additionalInfo?->custom_hobbies &&
                count($user->additionalInfo->custom_hobbies) > 0) )
                <div class="md:col-span-2">
                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Hobbies</label>
                    <div class="mt-1 flex flex-wrap gap-2">
                        @foreach ($user->hobbies as $hobby)
                        <span
                            class="px-3 py-1 rounded-full text-sm bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            {{ $hobby->name }}
                        </span>
                        @endforeach
                        @if ($user->additionalInfo?->custom_hobbies)
                        @foreach ($user->additionalInfo->custom_hobbies as $hobby)
                        <span
                            class="px-3 py-1 rounded-full text-sm bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            {{ $hobby }}
                        </span>
                        @endforeach
                        @endif
                    </div>
                </div>
                @endif

            </div>
            @else
            {{-- Message shown for private profiles when not allowed to view details --}}
            <div class="p-6 text-center text-gray-500 dark:text-gray-400 italic">
                <flux:icon.lock-closed class="mx-auto h-8 w-8 mb-2" />
                This profile is private. Follow this user to see their details.
            </div>
            @endif
        </div>
    </div>
</div>