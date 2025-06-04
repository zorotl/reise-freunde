<?php
use Livewire\Volt\Component;
use Livewire\Attributes\{layout, middleware, title};

new
#[Layout('components.layouts.admin.header')]
#[Title('Admin - Bug Reports')]
#[Middleware(['auth', 'admin_or_moderator'])]
class extends Component {}
?>

<div>
    <h1 class="text-2xl font-semibold mb-6">{{ __('Bug Reports') }}</h1>
    <livewire:admin.bug-reports.index />
</div>
