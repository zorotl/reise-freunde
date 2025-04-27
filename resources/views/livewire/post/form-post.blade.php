<div>
  <section class="w-full">
    <div class="flex items-start max-md:flex-col">
      <div class="flex-1 self-stretch max-md:pt-6">
        <div class="mt-5 w-full max-w-lg">
          <!-- Kein @submit.prevent hier -->
          <form wire:submit.prevent {{-- entferne hier Mappings --}} x-data="expirySelect(@entangle('action'))"
            class="w-full space-y-3" novalidate>
            @csrf
            @if ($action === 'update')
            @method('PUT')
            @endif

            <flux:input wire:model="title" label="Title" type="text" autofocus autocomplete="title" required
              maxlength="255" />

            <div x-data="{ content: @entangle('content'), min: 50 }" class="space-y-1">
              <flux:textarea x-model="content" wire:model="content" label="Content" autocomplete="content" required
                minlength="50" />
              <p x-text="content.length >= min
                    ? 'Minimum length reached'
                    : `${min - content.length} more characters needed`" class="text-sm" :class="{
                    'text-green-600': content.length >= min,
                    'text-gray-600': content.length < min
                  }"></p>
            </div>

            <flux:select x-model="selected" label="Expire Date">
              <flux:select.option value="2_weeks">2 weeks</flux:select.option>
              <flux:select.option value="1_month">1 month</flux:select.option>
              <flux:select.option value="3_months">3 months</flux:select.option>
              <flux:select.option value="until_start">until start date</flux:select.option>
            </flux:select>

            <flux:input wire:model="fromDate" label="From Date" type="date" autocomplete="fromDate" required />

            <flux:input wire:model="toDate" label="To Date" type="date" autocomplete="toDate" required />

            {{-- Searchable Country Dropdown --}}
            <div wire:ignore x-data="{
              tomSelectInstance: null,
              countryValue: @entangle('country'), // Entangle with Livewire's $country property
              initTomSelect() {
                  if (typeof TomSelect === 'undefined') { console.error('TomSelect not loaded'); return; }
                  this.tomSelectInstance = new TomSelect(this.$refs.countrySelectElement, {
                      create: false,
                      valueField: 'code',
                      labelField: 'name',
                      searchField: ['name'],
                      placeholder: '{{ __('Select Country...') }}',
                      options: @js(collect($countryList)->map(fn($name, $code) => ['code' => $code, 'name' => $name])->values()->all()),
                      onChange: (value) => {
                          // Update Livewire property when TomSelect changes
                          if (this.countryValue !== value) {
                              this.countryValue = value;
                          }
                      }
                  });
          
                  // Watch for changes coming FROM Livewire (e.g., when editing)
                  this.$watch('countryValue', (newValue) => {
                      if (this.tomSelectInstance.getValue() !== newValue) {
                          this.tomSelectInstance.setValue(newValue, true); // Update TomSelect silently
                      }
                  });
          
                  // Set initial value when the component/modal loads
                  if (this.countryValue) {
                       this.tomSelectInstance.setValue(this.countryValue, true);
                  }
          
                  // Optional: Listen for reset events if needed elsewhere
                  // Livewire.on('reset-my-form-event', () => {
                  //     if (this.tomSelectInstance) { this.tomSelectInstance.clear(); }
                  // });
              }
          }" x-init="initTomSelect()" class="mb-4"> {{-- Added mb-4 for spacing --}}

              <label for="country-select-{{ $this->getId() }}"
                class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('Country (optional)') }}
              </label>
              {{-- The underlying select element that TomSelect will enhance --}}
              <select id="country-select-{{ $this->getId() }}" x-ref="countrySelectElement"
                placeholder="{{ __('Select Country...') }}"></select>
              @error('country') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <flux:input wire:model="city" label="City (optional)" type="text" autocomplete="city" maxlength="255" />

            <div class="flex items-center gap-4">
              <!-- Klick löst erst prepareExpiry aus, dann Livewire save -->
              <flux:button variant="primary" type="button" class="w-full mt-3"
                @click="prepareExpiry(); triggerAction()">
                {{ __($buttonText) }}
              </flux:button>
              <a href="{{ route('post.show') }}" class="text-gray-500 hover:underline">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
  function expirySelect(action) {
    return {
      action,               // 'save' oder 'update'
      selected: '2_weeks',  
      prepareExpiry() {
        let date = new Date()
        switch (this.selected) {
          case '2_weeks': date.setDate(date.getDate() + 14); break
          case '1_month': date.setMonth(date.getMonth() + 1); break
          case '3_months': date.setMonth(date.getMonth() + 3); break
          case 'until_start': date = new Date(@this.fromDate); break
        }
        if (date > new Date(@this.fromDate)) {
          date = new Date(@this.fromDate)
        }
        const yyyy = date.getFullYear()
        const mm = String(date.getMonth() + 1).padStart(2, '0')
        const dd = String(date.getDate()).padStart(2, '0')
        @this.set('expiryDate', `${yyyy}-${mm}-${dd}`)
      },
      triggerAction() {
        // ruft die richtige Livewire-Methode auf
        if (this.action === 'save') {
          @this.call('save')
        } else {
          @this.call('update')
        }
      }
    }
  }
</script>