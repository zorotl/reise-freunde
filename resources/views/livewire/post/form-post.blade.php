<div>
    <section class="w-full">
        <div class="flex items-start max-md:flex-col">
            <div class="flex-1 self-stretch max-md:pt-6">
                <div class="mt-5 w-full max-w-lg">
                    <!-- Kein @submit.prevent hier -->
                    <form wire:submit.prevent="save" x-data="expirySelect()" novalidate>
                        @csrf

                        <flux:input wire:model="title" label="Title" type="text" autofocus autocomplete="title" required
                            maxlength="255" />

                        <div x-data="{ content: @entangle('content'), min: 50 }" class="space-y-1">
                            <flux:textarea x-model="content" wire:model="content" label="Content" autocomplete="content"
                                required minlength="50" />
                            <p x-text="content.length >= min
                    ? 'Minimum length reached'
                    : `${min - content.length} more characters needed`" class="text-sm" :class="{
                    'text-green-600': content.length >= min,
                    'text-gray-600': content.length < min
                  }"></p>
                        </div>

                        <!-- Expiry Selection -->
                        <div class="space-y-1">
                            <label class="block text-sm font-medium">Expiry</label>
                            <select x-model="selected" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="2_weeks">2 weeks</option>
                                <option value="1_month">1 month</option>
                                <option value="3_months">3 months</option>
                                <option value="until_start">until start date</option>
                            </select>
                        </div>

                        <flux:input wire:model="fromDate" label="From Date" type="date" autocomplete="fromDate"
                            required />

                        <flux:input wire:model="toDate" label="To Date" type="date" autocomplete="toDate" required />

                        <flux:input wire:model="country" label="Country (optional)" type="text" autocomplete="country"
                            maxlength="255" />

                        <flux:input wire:model="city" label="City (optional)" type="text" autocomplete="city"
                            maxlength="255" />

                        <div class="flex items-center gap-4">
                            <!-- Klick löst erst prepareExpiry aus, dann Livewire save -->
                            <flux:button variant="primary" type="button" class="w-full"
                                @click="prepareExpiry(); $wire.save()">
                                Create Post
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
    function expirySelect() {
      return {
        selected: '2_weeks',
        prepareExpiry() {
          let date = new Date();
          switch (this.selected) {
            case '2_weeks':
              date.setDate(date.getDate() + 14);
              break;
            case '1_month':
              date.setMonth(date.getMonth() + 1);
              break;
            case '3_months':
              date.setMonth(date.getMonth() + 3);
              break;
            case 'until_start':
              date = new Date(@this.fromDate);
              break;
          }
          if (date > new Date(@this.fromDate)) {
            date = new Date(@this.fromDate);
          }
          const yyyy = date.getFullYear();
          const mm = String(date.getMonth() + 1).padStart(2, '0');
          const dd = String(date.getDate()).padStart(2, '0');
          @this.set('expiryDate', `${yyyy}-${mm}-${dd}`);
        }
      }
    }
</script>