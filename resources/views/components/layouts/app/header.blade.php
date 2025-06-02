<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:header container class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">        
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <a href="{{ route('home') }}" class="ms-2 me-5 flex items-center space-x-2 rtl:space-x-reverse lg:ms-0"
            wire:navigate>
            <x-app-logo />
        </a>

        <flux:navbar class="-mb-px max-lg:hidden">
            {{-- New Dashboard --}}
            <flux:navbar.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                wire:navigate>
                {{ __('Dashboard') }}
            </flux:navbar.item>            

            {{-- "All Posts" --}}
            <flux:navbar.item icon="rectangle-stack" :href="route('post.show')"
                :current="request()->routeIs('post.show')" wire:navigate>
                {{ __("All Posts") }}
            </flux:navbar.item>

            {{-- Messages --}}
            <flux:navbar.item icon="envelope" :href="route('mail.inbox')" :current="request()->routeIs('mail.inbox')"
                wire:navigate>
                {{ __("Messages") }}
                <livewire:post.unread-messages-count />
            </flux:navbar.item>

            {{-- Friends --}}
            @auth
            <flux:navbar.item icon="users" :href="route('user.following', ['id' => auth()->user()->id])"
                :current="request()->routeIs('user.following')" wire:navigate>{{ __("Friends") }}
            </flux:navbar.item>
            @endauth

            {{-- "Find User" --}}
            <flux:navbar.item icon="users" :href="route('user.directory')"
                :current="request()->routeIs('user.directory')" wire:navigate>
                {{ __("Find User") }}
            </flux:navbar.item>
        </flux:navbar>

        <flux:spacer />

        {{-- User Menu Right Side --}}
        <x-language-switcher />

        @auth
        {{-- Notifications --}}
        <flux:navbar.item icon="bell" :href="route('notifications')" :current="request()->routeIs('notifications')"
            wire:navigate>
            {{-- {{ __('Notifications') }} --}}
            @if ( auth()->user()->unreadNotifications()->count() > 0)
                {{-- Unread Notifications Count --}}
                <span class="ml-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                    {{ auth()->user()->unreadNotifications()->count() }}
                </span>                
            @endif
        </flux:navbar.item>           

        {{-- User Menu Dropdown --}}
        <flux:dropdown position="top" align="end">
            {{-- ... rest of dropdown remains the same ... --}}
            <flux:profile class="cursor-pointer" avatar="{{ auth()->user()->profilePictureUrl() }}" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <img class="h-full w-full rounded-lg object-cover"
                                    src="{{ auth()->user()->profilePictureUrl() }}"
                                    alt="{{ auth()->user()->additionalInfo?->username ?? 'profile_picture' }}" />
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="'/user/profile/' . auth()->user()->id" icon="user" wire:navigate>{{
                        __('Show Profile') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('post.myown')" icon="user-circle" wire:navigate>{{
                        __('My Posts') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                {{-- Add Admin/Moderator link conditionally --}}
                @if(auth()->user()->isAdminOrModerator())
                <flux:menu.radio.group>
                    <flux:menu.item :href="route('admin.dashboard')" icon="shield-check" wire:navigate>
                        {{ __('Admin Area') }}
                    </flux:menu.item>
                </flux:menu.radio.group>
                <flux:menu.separator /> {{-- Separator after Admin link if shown --}}
                @endif


                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
        @endauth
        @guest
        {{-- Show Login/Register if user is not authenticated --}}
        <div class="flex items-center ms-2 space-x-2">
            <a href="{{ route('login') }}"
                class="inline-flex items-center justify-center px-3 py-1.5 border border-gray-400 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-100 dark:border-gray-500 dark:text-gray-400 dark:hover:bg-gray-700"
                wire:navigate>
                {{ __('Log in') }}
            </a>
            @if (Route::has('register'))
            <a href="{{ route('register') }}"
                class="inline-flex items-center justify-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600"
                wire:navigate>
                {{ __('Register') }}
            </a>
            @endif
        </div>
        @endguest
    </flux:header>

         @if (session('error'))
    <div class="mx-auto mt-3 max-w-7xl px-3 lg:px-4">
        <div class="rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-500 dark:bg-red-900 dark:text-red-100">
            {{ session('error') }}
        </div>
    </div>
@endif

@if (session('success'))
    <div class="mx-auto mt-3 max-w-7xl px-3 lg:px-4">
        <div class="rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-500 dark:bg-green-900 dark:text-green-100">
            {{ session('success') }}
        </div>
    </div>
@endif

    {{-- ... (Mobile Menu and rest of the layout) ... --}}
    <flux:sidebar stashable sticky
        class="lg:hidden border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ route('home') }}" class="ms-1 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
            <x-app-logo />
        </a>

        <flux:navlist variant="outline">
            <flux:navlist.group :heading="__('Platform')">
                <flux:navlist.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                    {{ __('Dashboard') }}
                </flux:navlist.item>

                <flux:navlist.item icon="rectangle-stack" :href="route('post.show')" :current="request()->routeIs('post.show')" wire:navigate>
                    {{ __("All Posts") }}
                </flux:navlist.item>

                <flux:navlist.item icon="users" :href="route('user.directory')" :current="request()->routeIs('user.directory')" wire:navigate>
                    {{ __("Find User") }}
                </flux:navlist.item>
            
                @auth
                    <flux:navlist.item icon="users" :href="route('user.following', ['id' => auth()->user()->id])" :current="request()->routeIs('user.following')" wire:navigate>
                        {{ __("Friends") }}
                    </flux:navlist.item>            
                @endauth
                
                <flux:navlist.item icon="envelope" :href="route('mail.inbox')" :current="request()->routeIs('mail.inbox')" wire:navigate>
                    {{ __("Messages") }}
                    <livewire:post.unread-messages-count />
                </flux:navlist.item>                
            </flux:navlist.group>
        </flux:navlist>
    </flux:sidebar>

    <div class="mx-auto w-full h-full max-w-7xl px-3 lg:px-4"> {{-- Removed flex items-center --}}
        {{ $slot }}
    </div>

    @fluxScripts    
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('formSync', { syncing: false })
        })
    </script>
</body>
</html>