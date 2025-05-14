<?php

namespace App\Livewire\Admin\Confirmations;

use Livewire\Component;
use App\Models\UserConfirmation;

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

        $this->load();
    }

    public function reject($id)
    {
        $confirmation = UserConfirmation::findOrFail($id);
        $confirmation->status = 'rejected';
        $confirmation->save();

        $this->load();
    }

    public function render()
    {
        return view('livewire.admin.confirmations.index');
    }
}
