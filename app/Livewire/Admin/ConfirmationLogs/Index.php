<?php

namespace App\Livewire\Admin\ConfirmationLogs;

use Livewire\Component;
use App\Models\ConfirmationLog;
use Livewire\Attributes\{layout, middleware, title};

#[Layout('components.layouts.admin.header')]
#[Title('Admin - Real World Confirmation Logs')]
#[Middleware(['auth', 'admin_or_moderator'])]
class Index extends Component
{
    public $logs;

    public function mount()
    {
        $this->logs = ConfirmationLog::with(['admin', 'confirmation.requester', 'confirmation.confirmer'])
            ->latest()
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.confirmation-logs.index');
    }
}

