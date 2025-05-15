{{-- Admin Navbar --}}
<flux:navbar class="-mb-px max-lg:hidden">

    {{-- Dashboard --}}
    <flux:navbar.item icon="layout-grid" :href="route('admin.dashboard')" :current="request()->routeIs('admin.dashboard')" wire:navigate>
        {{ __('Dashboard') }}
    </flux:navbar.item>

    {{-- 👤 User Management --}}
    <flux:dropdown class="max-lg:hidden">
        <flux:navbar.item icon="users" icon:trailing="chevron-down">
            {{ __('User Management') }}
        </flux:navbar.item>
        <flux:navmenu>
            <flux:navmenu.item :href="route('admin.users')">{{ __('Users') }}</flux:navmenu.item>
            <flux:navmenu.item :href="route('admin.user-approvals')">{{ __('User Approvals') }}</flux:navmenu.item>
            <flux:navmenu.item :href="route('admin.verifications')">{{ __('User Verifications') }}</flux:navmenu.item>
            <flux:navmenu.item :href="route('admin.confirmations')">{{ __('Real World Confirmations') }}</flux:navmenu.item>
        </flux:navmenu>
    </flux:dropdown>

    {{-- 📰 Post --}}
    <flux:dropdown class="max-lg:hidden">
        <flux:navbar.item icon="document-text" icon:trailing="chevron-down">
            {{ __('Post') }}
        </flux:navbar.item>
        <flux:navmenu>
            <flux:navmenu.item :href="route('admin.posts')">{{ __('Posts') }}</flux:navmenu.item>
        </flux:navmenu>
    </flux:dropdown>

    {{-- 📬 Messages --}}
    <flux:dropdown class="max-lg:hidden">
        <flux:navbar.item icon="envelope" icon:trailing="chevron-down">
            {{ __('Messages') }}
        </flux:navbar.item>
        <flux:navmenu>
            <flux:navmenu.item :href="route('admin.messages')">{{ __('Messages') }}</flux:navmenu.item>
        </flux:navmenu>
    </flux:dropdown>

    {{-- 🧭 Inhalte --}}
    <flux:dropdown class="max-lg:hidden">
        <flux:navbar.item icon="globe-alt" icon:trailing="chevron-down">
            {{ __('Inhalte') }}
        </flux:navbar.item>
        <flux:navmenu>
            <flux:navmenu.item :href="route('admin.travel-styles')">{{ __('Travel Styles') }}</flux:navmenu.item>
            <flux:navmenu.item :href="route('admin.hobbies')">{{ __('Hobbies') }}</flux:navmenu.item>
        </flux:navmenu>
    </flux:dropdown>

    {{-- 📰 Reports --}}
    <flux:dropdown class="max-lg:hidden">
        <flux:navbar.item icon="exclamation-triangle" icon:trailing="chevron-down">
            {{ __('Reports') }}
        </flux:navbar.item>
        <flux:navmenu>
            <flux:navmenu.item :href="route('admin.reports')">{{ __('Reports') }}</flux:navmenu.item>
        </flux:navmenu>
    </flux:dropdown>

</flux:navbar>