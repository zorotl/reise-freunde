<div class="p-4 bg-white shadow rounded-xl">
    <h2 class="text-xl font-semibold mb-2">⚠️ Route Conflicts</h2>

    @if(count($conflicts) > 0)
    <ul class="list-disc list-inside text-sm text-red-600">
        @foreach($conflicts as $conflict)
        <li>
            <span class="font-medium">{{ $conflict['static'] }}</span>
            may be overridden by
            <span class="font-medium">{{ $conflict['dynamic'] }}</span>
        </li>
        @endforeach
    </ul>
    @else
    <p class="text-sm text-green-700">✅ No conflicts detected.</p>
    @endif
</div>