<x-layouts.app.header :title="$title ?? null">
    <flux:main>
        {{ $slot }}
    </flux:main>

    {{-- Add the report modal component here --}}
    <livewire:report-post-modal />

</x-layouts.app.header>