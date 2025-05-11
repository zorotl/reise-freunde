<div class="flex flex-wrap gap-2">
    @foreach ($badges as $badge)
        <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-sm font-medium">
            {{ $badge['icon'] }} {{ __($badge['label']) }}
        </span>
    @endforeach
</div>
