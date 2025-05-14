<?php

namespace App\Livewire\Admin\Confirmations;

use Livewire\Component;
use App\Models\UserConfirmation;
use App\Models\ConfirmationLog;
use Livewire\Attributes\{layout, middleware, title};

#[Layout('components.layouts.admin.header')]
#[Title('Admin - User Real World Confirmation')]
#[Middleware(['auth', 'admin_or_moderator'])]
class Index extends Component
{
    public $confirmations;

    public function mount()
    {
        $this->load();
    }

    public function load()
    {
        $this->confirmations = UserConfirmation::with(['requester', 'confirmer'])
            ->where('status', 'pending')
            ->latest()
            ->get();
    }

    public function approve($id)
    {
        $confirmation = UserConfirmation::findOrFail($id);
        $confirmation->status = 'accepted';
        $confirmation->save();

        ConfirmationLog::create([
            'admin_id' => auth()->id(),
            'confirmation_id' => $confirmation->id,
            'action' => 'approved',
        ]);

        $this->load();
    }

    public function reject($id)
    {
        $confirmation = UserConfirmation::findOrFail($id);
        $confirmation->status = 'rejected';
        $confirmation->save();

        ConfirmationLog::create([
            'admin_id' => auth()->id(),
            'confirmation_id' => $confirmation->id,
            'action' => 'rejected',
        ]);

        $this->load();
    }

    public function render()
    {
        return view('livewire.admin.confirmations.index');
    }
}
