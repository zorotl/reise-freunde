<?php

namespace App\Livewire\Profile;

use Livewire\Component;

class Notifications extends Component
{
    public function markAllRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
    }

    public function render()
    {
        return view('livewire.profile.notifications', [
            'notifications' => auth()->user()->notifications()->latest()->get()
        ]);
    }
}

