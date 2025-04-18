<x-layouts.app :title="__('Dashboard')">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            {{ __('Dashboard') }}
        </h1>

        <div class="mt-4">
            <livewire:pinboard.show-pinboard />
        </div>
    </div>
</x-layouts.app>