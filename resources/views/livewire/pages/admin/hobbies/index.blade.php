<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{layout, middleware, title};

new
#[Layout('components.layouts.admin.header')]
#[Title('Admin - Hobbies')]
#[Middleware(['auth', 'admin_or_moderator'])]
class extends Component
{
    //
}
?>

<div>
    {{-- Page Title --}}
    <h1 class="text-2xl font-semibold mb-6">Manage Hobbies</h1>

    {{-- Include the ManageHobbies Livewire component --}}
    <livewire:admin.hobbies.manage-hobbies />
</div>