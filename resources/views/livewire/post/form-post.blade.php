<div>
  {{-- Add a heading or breadcrumbs if needed for the admin context --}}
  <h1 class="text-2xl font-bold mb-5 flex items-center gap-2">
    {{ $buttonText === 'Update Post' ? __('Edit Post') : __('Create Post') }}
    @if (($origin ?? '') === 'admin')
        <span class="text-sm font-medium text-white bg-indigo-500 px-2 py-0.5 rounded-md">
            Admin
        </span>
    @endif
  </h1>
  <section class="w-full">
    <div class="flex items-start max-md:flex-col">
      <div class="flex-1 self-stretch max-md:pt-6">
        <div class="mt-5 w-full max-w-lg">
          <!-- Kein @submit.prevent hier -->
          <form wire:submit.prevent {{-- entferne hier Mappings --}} x-data="expirySelect(@entangle('action'))"
            class="w-full space-y-5" novalidate>
            @csrf
            @if ($action === 'update')
            @method('PUT')
            @endif

            <flux:input wire:model="title" label="{{__('Title')}}" type="text" autofocus autocomplete="title" required
              maxlength="255" />

            <div x-data="{ content: @entangle('content'), min: 50 }" class="space-y-1">
              <flux:textarea x-model="content" wire:model="content" label="{{__('Content')}}" autocomplete="content" required
                minlength="50" />
              <p x-text="content.length >= min
                    ? 'Minimum length reached'
                    : `${min - content.length} more characters needed`" class="text-sm" :class="{
                    'text-green-600': content.length >= min,
                    'text-gray-600': content.length < min
                  }"></p>
            </div>

            <h2 class="text-lg font-semibold mt-10 mb-4 text-gray-700 dark:text-gray-200">
                {{ __('Post Details') }}
            </h2>            

            <flux:input wire:model="fromDate" label="{{__('From Date')}}" type="date" autocomplete="fromDate" required />

            <flux:input wire:model="toDate" label="{{__('To Date')}}" type="date" autocomplete="toDate" required />

            <flux:select x-model="selected" label="{{__('Expire Date')}}">
              <flux:select.option value="2_weeks">2 weeks</flux:select.option>
              <flux:select.option value="1_month">1 month</flux:select.option>
              <flux:select.option value="3_months">3 months</flux:select.option>
              <flux:select.option value="until_start">until start date</flux:select.option>
            </flux:select>

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
                class="mt-1 block w-full rounded-md border-gray-300 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                placeholder="{{ __('Select Country...') }}"></select>
              @error('country') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <flux:input wire:model="city" label="{{ __('City (optional)') }}" type="text" autocomplete="city" maxlength="255" />

            {{-- Is Active Checkbox (only for Admin Edit) --}}
            @if ($origin === 'admin')
            <div class="mb-4">
              <div class="flex items-start space-x-3">
              <input type="checkbox" wire:model="is_active" id="is_active"
                  class="mt-1 rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50 dark:bg-neutral-700 dark:border-neutral-600 dark:text-green-500" />
              <div class="flex-1">
                  <label for="is_active" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                      {{ __('Is Active') }}
                  </label>
                  <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                      {{ __('Make this post visible to others') }}
                  </p>
              </div>
          </div>

              @error('is_active') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            @endif

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mt-6">
              <button type="button" onclick="window.history.back()" 
                class="inline-flex items-center justify-center px-4 py-2 border text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-neutral-700 dark:text-gray-300 dark:hover:bg-neutral-600 border-gray-300 dark:border-neutral-600">
                {{ __('Cancel') }}
              </button>            
              <flux:button variant="primary" type="button" class="w-full sm:w-auto"
                  @click="prepareExpiry(); triggerAction()">
                  {{ __($buttonText) }}
              </flux:button>              
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