<?php

namespace App\Livewire\Admin\ConfirmationLogs;

use Livewire\Component;
use App\Models\ConfirmationLog;

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

