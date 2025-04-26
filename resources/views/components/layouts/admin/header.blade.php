{{-- Content from your original app/header.blade.php component view --}}
{{-- resources/views/components/layouts/admin/header.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

{{-- Apply different classes for visual distinction --}}

<body class="min-h-screen bg-gray-100 dark:bg-zinc-900 text-gray-900 dark:text-gray-100">
    {{-- Changed header color --}}
    <flux:header container class="border-b border-red-700 bg-red-600 dark:border-red-800 dark:bg-red-900 text-white">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        {{-- Link back to admin dashboard --}}
        <a href="{{ route('admin.dashboard') }}"
            class="ms-2 me-5 flex items-center space-x-2 rtl:space-x-reverse lg:ms-0" wire:navigate>
            {{--
            <x-app-logo /> --}}
            <span class="text-lg font-semibold">Admin</span> {{-- Added "Admin" badge --}}
        </a>

        {{-- Admin Navbar - Add admin-specific links here later --}}
        <flux:navbar class="-mb-px max-lg:hidden">
            {{-- Link to Admin Dashboard --}}
            <flux:navbar.item icon="layout-grid" :href="route('admin.dashboard')"
                :current="request()->routeIs('admin.dashboard')" wire:navigate>
                {{ __('Dashboard') }}
            </flux:navbar.item>

            {{-- Link to Manage Users --}}
            <flux:navbar.item icon="users" :href="route('admin.users')" :current="request()->routeIs('admin.users')"
                wire:navigate>
                {{ __('Users') }}
            </flux:navbar.item>

            {{-- Link to Manage Posts --}}
            <flux:navbar.item icon="document" :href="route('admin.posts')" :current="request()->routeIs('admin.posts')"
                wire:navigate>
                {{ __('Posts') }}
            </flux:navbar.item>

            {{-- Link to Manage Messages --}}
            <flux:navbar.item icon="envelope" :href="route('admin.messages')"
                :current="request()->routeIs('admin.messages')" wire:navigate>
                {{ __('Messages') }}
            </flux:navbar.item>

            {{-- Link to Manage Hobbies --}}
            <flux:navbar.item icon="bookmark" :href="route('admin.hobbies')"
                :current="request()->routeIs('admin.hobbies')" wire:navigate>
                {{ __('Hobbies') }}
            </flux:navbar.item>

            {{-- Link to Manage Travel Styles --}}
            <flux:navbar.item icon="map" :href="route('admin.travel-styles')"
                :current="request()->routeIs('admin.travel-styles')" wire:navigate>
                {{ __('Travel Styles') }}
            </flux:navbar.item>
        </flux:navbar>

        <flux:spacer />

        {{-- User Menu - Keep as is or adapt --}}
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

                {{-- Link back to main site dashboard --}}
                <flux:menu.radio.group>
                    <flux:menu.item :href="route('dashboard')" icon="layout-grid" wire:navigate>{{ __('Main Dashboard')
                        }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="'/user/profile/' . auth()->user()->id" icon="user" wire:navigate>{{
                        __('Show Profile') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                {{-- No Admin link in admin area menu --}}

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
    <!-- Mobile Menu -->
    <flux:sidebar stashable sticky
        class="lg:hidden border-e border-red-700 bg-red-600 dark:border-red-800 dark:bg-red-900 text-white"> {{--
        Changed colors --}}
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        {{-- Link back to admin dashboard --}}
        <a href="{{ route('admin.dashboard') }}" class="ms-1 flex items-center space-x-2 rtl:space-x-reverse"
            wire:navigate>
            <x-app-logo />
            <span class="text-lg font-semibold">Admin</span>
        </a>

        {{-- Admin Mobile Navlist - Add admin-specific links here later --}}
        <flux:navlist variant="outline">
            <flux:navlist.group :heading="__('Administration')"> {{-- You might want a group for admin links --}}
                {{-- Link to Admin Dashboard --}}
                <flux:navlist.item icon="layout-grid" :href="route('admin.dashboard')"
                    :current="request()->routeIs('admin.dashboard')" wire:navigate>
                    {{ __('Dashboard') }}
                </flux:navlist.item>

                {{-- Link to Manage Users --}}
                <flux:navlist.item icon="users" :href="route('admin.users')"
                    :current="request()->routeIs('admin.users')" wire:navigate>
                    {{ __('Users') }}
                </flux:navlist.item>

                {{-- Link to Manage Posts --}}
                <flux:navlist.item icon="document" :href="route('admin.posts')"
                    :current="request()->routeIs('admin.posts')" wire:navigate>
                    {{ __('Posts') }}
                </flux:navlist.item>

                {{-- Link to Manage Messages --}}
                <flux:navlist.item icon="envelope" :href="route('admin.messages')"
                    :current="request()->routeIs('admin.messages')" wire:navigate>
                    {{ __('Messages') }}
                </flux:navlist.item>

                {{-- Link to Manage Hobbies --}}
                <flux:navlist.item icon="bookmark" :href="route('admin.hobbies')"
                    :current="request()->routeIs('admin.hobbies')" wire:navigate>
                    {{ __('Hobbies') }}
                </flux:navlist.item>

                {{-- Link to Manage Travel Styles --}}
                <flux:navlist.item icon="map" :href="route('admin.travel-styles')"
                    :current="request()->routeIs('admin.travel-styles')" wire:navigate>
                    {{ __('Travel Styles') }}
                </flux:navlist.item>
            </flux:navlist.group>

            {{-- Existing Platform Group --}}
            {{-- <flux:navlist.group :heading="__('Platform')"> ... </flux:navlist.group> --}}

        </flux:navlist>

        <flux:spacer />

        {{-- Optional: keep documentation links or remove --}}
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


    {{-- Main content area - The $slot prop will render the page content here --}}
    <div class="mx-auto w-full h-full max-w-7xl px-3 lg:px-4 py-8">
        {{ $slot }}
    </div>


    @fluxScripts
</body>

</html>