<?php

namespace App\Livewire\User;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class UserProfile extends Component
{
    public User $user;



    public function mount()
    {
        $this->user = Auth::user()->load('additionalInfo');
    }

    public function render()
    {
        return view('livewire.user.user-profile');
    }
}
