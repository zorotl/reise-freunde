<?php

namespace App\Livewire\Admin\UserApproval;

use Livewire\Component;
use App\Models\User;
use Livewire\Attributes\{layout, middleware, title};

#[Layout('components.layouts.admin.header')]
#[Title('Admin - User Approvals')]
#[Middleware(['auth', 'admin_or_moderator'])]
class Index extends Component
{
    public $users;

    public function mount()
    {
        $this->loadPendingUsers();
    }

    public function loadPendingUsers()
    {
        $this->users = User::where('status', 'pending')
            ->whereNotNull('email_verified_at')
            ->latest()
            ->get();
    }

    public function approve($userId)
    {
        $user = User::findOrFail($userId);
        $user->status = 'approved';
        $user->approved_at = now();
        $user->save();

        $this->loadPendingUsers();
    }

    public function reject($userId)
    {
        $user = User::findOrFail($userId);
        $user->status = 'rejected';
        $user->rejected_at = now();
        $user->save();

        $this->loadPendingUsers();
    }

    public function render()
    {
        return view('livewire.admin.user-approval.index');
    }
}

