<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{layout, middleware, title};

new
#[Layout('components.layouts.admin.header')]
#[Title('Admin - Travel Styles')]
#[Middleware(['auth', 'admin_or_moderator'])]
class extends Component
{
    //
}
?>

<div>
    {{-- Page Title --}}
    <h1 class="text-2xl font-semibold mb-6">Manage Travel Styles</h1>

    {{-- Include the ManageTravelStyles Livewire component --}}
    <livewire:admin.travel-styles.manage-travel-styles />
</div>