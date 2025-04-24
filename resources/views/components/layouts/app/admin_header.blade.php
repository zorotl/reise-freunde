<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
    <title>{{ config('app.name', 'Laravel') }} - Admin</title> {{-- Add Admin to title --}}
</head>

{{-- Added a different background class and a top banner --}}

<body class="min-h-screen bg-gray-100 dark:bg-zinc-900 text-gray-900 dark:text-gray-100">

    <div class="bg-red-600 text-white text-center py-2 text-sm font-semibold">
        ADMINISTRATION AREA
    </div>

    <flux:header container class="border-b border-zinc-200 bg-red-700 dark:border-red-800 dark:bg-red-900"> {{-- Changed
        header color --}}
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <a href="{{ route('admin.dashboard') }}"
            class="ms-2 me-5 flex items-center space-x-2 rtl:space-x-reverse lg:ms-0" wire:navigate>
            <x-app-logo /> {{-- Assuming x-app-logo is generic enough or create an admin version --}}
            <span class="text-lg font-semibold text-white">Admin</span> {{-- Added "Admin" badge --}}
        </a>

        {{-- Admin Navbar (will add links specific to admin later) --}}
        <flux:navbar class="-mb-px max-lg:hidden">
            {{-- Links will go here --}}
        </flux:navbar>

        <flux:spacer />

        {{-- Keep Search/User Menu if desired, or remove/adapt for admin --}}
        {{-- For now, let's keep the user menu, but perhaps remove the search bar --}}
        {{-- <flux:navbar class="me-1.5 space-x-0.5 rtl:space-x-reverse py-0!">
            <flux:tooltip :content="__('Search')" position="bottom">
                <flux:navbar.item class="!h-10 [&>div>svg]:size-5" icon="magnifying-glass" href="#"
                    :label="__('Search')" />
            </flux:tooltip>
        </flux:navbar> --}}

        @auth
        <flux:dropdown position="top" align="end">
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
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="'/user/profile/' . auth()->user()->id" icon="user" wire:navigate>{{
                        __('Show Profile') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
        @endauth

    </flux:header>

    <flux:sidebar stashable sticky
        class="lg:hidden border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ route('admin.dashboard') }}" class="ms-1 flex items-center space-x-2 rtl:space-x-reverse"
            wire:navigate>
            <x-app-logo />
            <span class="text-lg font-semibold text-white">Admin</span>
        </a>

        {{-- Admin Mobile Navlist (will add links specific to admin later) --}}
        <flux:navlist variant="outline">
            {{-- Links will go here --}}
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


    {{-- Main content area --}}
    <div class="mx-auto w-full h-full max-w-7xl px-3 lg:px-4 py-8"> {{-- Added padding-top for the banner --}}
        {{ $slot }}
    </div>


    @fluxScripts
</body>

</html>