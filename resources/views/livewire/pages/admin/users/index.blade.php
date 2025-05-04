<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{layout, middleware, title};

new 
#[Title('Admin - Users')]
#[Layout('components.layouts.admin')]
#[Middleware('auth', 'admin_or_moderator')]
class extends Component{}

?>

<div>
    {{-- Page Title --}}
    <h1 class="text-2xl font-semibold mb-6">Manage Users</h1>

    {{-- Include the ManageUsers Livewire component --}}
    <livewire:admin.users.manage-users />
</div>