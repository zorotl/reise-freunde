<div>
    {{-- Predefined Travel Styles --}}
    <h2 class="text-xl font-bold mb-4">Travel Styles</h2>
    <div class="flex flex-wrap gap-2">
        @foreach($travelStyles as $style)
        <button type="button" wire:click="toggleTravelStyle({{ $style->id }})"
            class="px-3 py-1 rounded-full text-sm border
                    {{ in_array($style->id, $selectedTravelStyles) ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700' }}">
            {{ $style->name }}
        </button>
        @endforeach
    </div>

    {{-- Custom Travel Styles --}}
    <div x-data="customTagInput(@js($customTravelStyle), '{{ $this->getId() }}')" x-init="init()" class="mt-4">
        <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Other Travel Styles</label>

        <div class="flex flex-wrap gap-2 mb-2">
            <template x-for="(tag, index) in tags" :key="index">
                <span class="bg-gray-200 text-sm rounded-full px-3 py-1 flex items-center">
                    <span x-text="tag"></span>
                    <button type="button" @click="removeTag(index)"
                        class="ml-2 text-red-600 hover:text-red-800">×</button>
                </span>
            </template>
        </div>

        <input type="text" x-model="input" @keydown.enter.prevent="addTag()" placeholder="Type and press Enter"
            class="p-2 border rounded w-full">
    </div>


    {{-- Predefined Hobbies --}}
    <h2 class="text-xl font-bold mt-6 mb-4">Hobbies, Sports & Fun</h2>
    <div class="flex flex-wrap gap-2">
        @foreach($hobbies as $hobby)
        <button type="button" wire:click="toggleHobby({{ $hobby->id }})"
            class="px-3 py-1 rounded-full text-sm border
                    {{ in_array($hobby->id, $selectedHobbies) ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700' }}">
            {{ $hobby->name }}
        </button>
        @endforeach
    </div>

    {{-- Custom Hobbies --}}
    <div x-data="customTagInput(@js($customHobby), '{{ $this->getId() }}', 'customHobby')" x-init="init()" class="mt-6">
        <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Other Hobbies, Sports & Fun</label>

        <div class="flex flex-wrap gap-2 mb-2">
            <template x-for="(tag, index) in tags" :key="index">
                <span class="bg-gray-200 text-sm rounded-full px-3 py-1 flex items-center">
                    <span x-text="tag"></span>
                    <button type="button" @click="removeTag(index)"
                        class="ml-2 text-red-600 hover:text-red-800">×</button>
                </span>
            </template>
        </div>

        <input type="text" x-model="input" @keydown.enter.prevent="addTag()" placeholder="Type and press Enter"
            class="p-2 border rounded w-full">
    </div>




    {{-- Save Button --}}
    <button wire:click="save" class="mt-6 bg-blue-600 hover:bg-blue-500 text-white px-6 py-2 rounded">
        Save
    </button>

    @if (session()->has('success'))
    <p class="text-green-600 mt-3">{{ session('success') }}</p>
    @endif
</div>

<script>
    function customTagInput(initialTags, componentId, wireModel) {
        return {
            input: '',
            tags: initialTags,

            init() {
                this.sync();
            },

            sync() {
                if (window.Livewire) {
                    const component = Livewire.find(componentId);
                    if (component) {
                        component.set(wireModel, this.tags);
                    }
                }
            },

            addTag() {
                const value = this.input.trim();
                if (value && !this.tags.includes(value)) {
                    this.tags.push(value);
                    this.input = '';
                    this.sync();
                }
            },

            removeTag(index) {
                this.tags.splice(index, 1);
                this.sync();
            }
        }
    }
</script>