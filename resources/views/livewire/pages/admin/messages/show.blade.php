<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{layout, middleware};

// Apply the admin layout and middleware
new
#[Layout('components.layouts.admin.header')]
#[Middleware(['auth', 'admin_or_moderator'])]
class extends Component
{
    // Define the messageId property to accept the route parameter
    public $messageId;

    // The mount method will be called to set the property
    public function mount($messageId)
    {
        $this->messageId = $messageId;
    }
}
?>

<div>
    {{-- Page Title (Optional, can be handled in the Livewire component) --}}
    {{-- <h1 class="text-2xl font-semibold mb-6">View Message</h1> --}}

    {{-- Include the ViewMessage Livewire component, passing the messageId --}}
    <livewire:admin.messages.view-message :messageId="$messageId" />
</div>