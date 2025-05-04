<?php

use Livewire\Volt\Component; // Use Volt's Component helper for page definition
use Livewire\Attributes\{layout, middleware, title};

// Apply the admin layout and middleware
new
#[Layout('components.layouts.admin.header')]
#[Title('Admin - Posts')]
#[Middleware(['auth', 'admin_or_moderator'])]
class extends Component
{
    // No need for mount or properties here, the Livewire component handles the data
}
?>

<div>
    {{-- Page Title --}}
    <h1 class="text-2xl font-semibold mb-6">Manage Posts</h1>

    {{-- Include the ManagePosts Livewire component --}}
    <livewire:admin.posts.manage-posts />
</div>