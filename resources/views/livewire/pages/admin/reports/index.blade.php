<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{layout, middleware, title};

// Apply the admin layout and middleware
new
#[Layout('components.layouts.admin')] // Make sure this points to your correct admin layout
#[Title('Admin - Reports')]
#[Middleware(['auth', 'admin_or_moderator'])]
class extends Component
{
    //
}
?>

<div>
    {{-- Page Title --}}
    <h1 class="text-2xl font-semibold mb-6">{{ __('Manage Reports') }}</h1>

    {{-- Include the ManagePostReports Livewire component --}}
    <livewire:admin.reports.manage-reports />
</div>