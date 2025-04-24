<?php

use Livewire\Volt\Component; // Use Volt's Component helper for page definition
use Livewire\Attributes\{layout, middleware};

// Apply the admin layout and middleware
new
#[Layout('components.layouts.admin.header')]
#[Middleware(['auth', 'admin_or_moderator'])]
class extends Component
{
    // No need for mount or properties here
}
?>

<div>
    {{-- Page Title --}}
    <h1 class="text-2xl font-semibold mb-6">Manage Messages</h1>

    {{-- Include the ManageMessages Livewire component --}}
    <livewire:admin.messages.manage-messages />
</div>