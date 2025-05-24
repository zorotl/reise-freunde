<div class="flex flex-wrap gap-2">
    @foreach ($badges as $badge)
        @php
            $isSystem = ($badge['type'] ?? null) === 'system';
        @endphp
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
            {{ $isSystem
                ? 'bg-gray-200 text-gray-600 dark:bg-neutral-700 dark:text-gray-300'
                : 'bg-green-200 text-green-800 dark:bg-green-900 dark:text-green-200' }}">
            {{ $badge['icon'] }} {{ __($badge['label']) }}
        </span>
    @endforeach
</div>
