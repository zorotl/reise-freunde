<div>
    <h2 class="text-xl font-bold mb-4">Travel Styles</h2>
    <div class="grid grid-cols-2 gap-2">
        @foreach($travelStyles as $style)
        <label>
            <input type="checkbox" wire:model="selectedTravelStyles" value="{{ $style->id }}">
            {{ $style->name }}
        </label>
        @endforeach
    </div>

    <input type="text" wire:model.defer="customTravelStyle" placeholder="Other travel style..."
        class="mt-2 p-1 border rounded">

    <h2 class="text-xl font-bold mt-6 mb-4">Hobbies, Sports & Fun</h2>
    <div class="grid grid-cols-2 gap-2">
        @foreach($hobbies as $hobby)
        <label>
            <input type="checkbox" wire:model="selectedHobbies" value="{{ $hobby->id }}">
            {{ $hobby->name }}
        </label>
        @endforeach
    </div>

    <input type="text" wire:model.defer="customHobby" placeholder="Other hobby or activity..."
        class="mt-2 p-1 border rounded">

    <button wire:click="save" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded">Save</button>

    @if (session()->has('success'))
    <p class="text-green-500 mt-2">{{ session('success') }}</p>
    @endif
</div>