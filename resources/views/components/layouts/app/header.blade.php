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
            {{-- New Dashboard Link --}}
            <flux:navbar.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                wire:navigate>
                {{ __('Dashboard') }}
            </flux:navbar.item>

            {{-- Renamed "All Posts" Link --}}
            <flux:navbar.item icon="rectangle-stack" :href="route('post.show')"
                :current="request()->routeIs('post.show')" wire:navigate>
                {{ __("All Posts") }}
            </flux:navbar.item>

            @auth
            {{-- Existing Auth Links --}}
            <flux:navbar.item icon="envelope" :href="route('mail.inbox')" :current="request()->routeIs('mail.inbox')"
                wire:navigate>
                {{ __("Inbox") }}
                <livewire:post.unread-messages-count />
            </flux:navbar.item>
            <flux:navbar.item icon="users" :href="route('user.following', ['id' => auth()->user()->id])"
                :current="request()->routeIs('user.following')" wire:navigate>{{ __("Following") }}
            </flux:navbar.item>
            @endauth
        </flux:navbar>

        <flux:spacer />

        {{-- Search Icon --}}
        <flux:navbar class="me-1.5 space-x-0.5 rtl:space-x-reverse py-0!">
            <flux:tooltip :content="__('Search')" position="bottom">
                <flux:navbar.item class="!h-10 [&>div>svg]:size-5" icon="magnifying-glass" href="#"
                    :label="__('Search')" /> {{-- Update search link if needed --}}
            </flux:tooltip>
        </flux:navbar>

        {{-- User Menu Dropdown --}}
        @auth
        <flux:dropdown position="top" align="end">
            {{-- ... rest of dropdown remains the same ... --}}
            <flux:profile class="cursor-pointer" :initials="auth()->user()->initials()" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
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
        <div class="flex items-center space-x-2">
            <a href="{{ route('login') }}"
                class="text-sm font-medium text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white"
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

    {{-- ... (Mobile Menu and rest of the layout) ... --}}
    <flux:sidebar stashable sticky
        class="lg:hidden border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ route('home') }}" class="ms-1 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
            <x-app-logo />
        </a>

        <flux:navlist variant="outline">
            <flux:navlist.group :heading="__('Platform')">
                {{-- Mobile: New Dashboard Link --}}
                <flux:navlist.item icon="layout-grid" :href="route('dashboard')"
                    :current="request()->routeIs('dashboard')" wire:navigate>
                    {{ __('Dashboard') }}
                </flux:navlist.item>
                {{-- Mobile: Renamed "All Posts" Link --}}
                <flux:navlist.item icon="rectangle-stack" :href="route('post.show')"
                    :current="request()->routeIs('post.show')" wire:navigate>
                    {{ __("All Posts") }}
                </flux:navlist.item>
                @auth
                <flux:navlist.item icon="user-circle" :href="route('post.myown')"
                    :current="request()->routeIs('post.myown')" wire:navigate>{{ __("My Posts") }}
                </flux:navlist.item>
                <flux:navlist.item icon="envelope" :href="route('mail.inbox')"
                    :current="request()->routeIs('mail.inbox')" wire:navigate>
                    {{ __("Inbox") }}
                    <livewire:post.unread-messages-count />
                </flux:navlist.item>
                <flux:navlist.item icon="users" :href="route('user.following', ['id' => auth()->user()->id])"
                    :current="request()->routeIs('user.following')" wire:navigate>{{ __("Following") }}
                </flux:navlist.item>
                {{-- Mobile: Admin Area Link --}}
                @if(auth()->user()->isAdminOrModerator())
                <flux:navlist.item icon="shield-check" :href="route('admin.dashboard')" wire:navigate>
                    {{ __('Admin Area') }}
                </flux:navlist.item>
                @endif
                @endauth
            </flux:navlist.group>
        </flux:navlist>

        <flux:spacer />

        <flux:navlist variant="outline">
            <flux:navlist.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit"
                target="_blank">
                {{ __('Repository') }}
            </flux:navlist.item>

            <flux:navlist.item icon="book-open-text" href="https://laravel.com/docs/starter-kits" target="_blank">
                {{ __('Documentation') }}
            </flux:navlist.item>
        </flux:navlist>
    </flux:sidebar>

    <div class="mx-auto w-full h-full max-w-7xl px-3 lg:px-4"> {{-- Removed flex items-center --}}
        {{ $slot }}
    </div>

    @fluxScripts
</body>

</html>