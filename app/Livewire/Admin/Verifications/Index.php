<?php

namespace App\Livewire\Admin\Verifications;

use Livewire\Component;
use App\Models\UserVerification;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\{layout, middleware, title};

#[Layout('components.layouts.admin.header')]
#[Title('Admin - User Verifications')]
#[Middleware(['auth', 'admin_or_moderator'])]
class Index extends Component
{
    public $verifications;

    public function mount()
    {
        $this->loadPendingVerifications();
    }

    public function loadPendingVerifications()
    {
        $this->verifications = UserVerification::with('user')
            ->where('status', 'pending')
            ->latest()
            ->get();
    }

    public function approve($id)
    {
        $verification = UserVerification::findOrFail($id);
        $verification->status = 'reviewed';
        $verification->reviewed_by = Auth::id();
        $verification->save();

        $this->loadPendingVerifications();
    }

    public function reject($id)
    {
        $verification = UserVerification::findOrFail($id);
        $verification->status = 'rejected';
        $verification->reviewed_by = Auth::id();
        $verification->save();

        $this->loadPendingVerifications();
    }

    public function render()
    {
        return view('livewire.admin.verifications.index');
    }
}
