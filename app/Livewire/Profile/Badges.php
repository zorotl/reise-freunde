<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use App\Models\User;

class Badges extends Component
{
    public User $user;

    public function render()
    {
        $badges = [];

        // Manual or auto approval
        if ($this->user->status === 'approved') {
            $badges[] = ['label' => 'Account Approved', 'icon' => '🔓', 'type' => 'system'];
        } elseif ($this->user->status === 'auto-approved') {
            $badges[] = ['label' => 'Signup Verified', 'icon' => '📝', 'type' => 'system'];
        }

        // Verification documents
        $verification = $this->user->verification;
        if ($verification) {
            if ($verification->id_document_path) {
                $badges[] = ['label' => 'ID Verified', 'icon' => '🪪', 'type' => 'trust'];
            }
            if (!empty($verification->social_links)) {
                $badges[] = ['label' => 'Social Linked', 'icon' => '🌐', 'type' => 'trust'];
            }
        }

        // Bürgschafts (Real-world confirmations)
        $confirmedCount = \App\Models\UserConfirmation::where(function ($q) {
            $q->where('requester_id', $this->user->id)
                ->orWhere('confirmer_id', $this->user->id);
        })->where('status', 'accepted')->count();

        if ($confirmedCount >= 10) {
            $badges[] = ['label' => 'Highly Trusted (10+)', 'icon' => '🏅', 'type' => 'trust'];
            $badges[] = ['label' => 'Real-Life Confirmed', 'icon' => '👥', 'type' => 'trust'];
        } elseif ($confirmedCount >= 3) {
            $badges[] = ['label' => 'Trusted (3+)', 'icon' => '🛡️', 'type' => 'trust'];
            $badges[] = ['label' => 'Real-Life Confirmed', 'icon' => '👥', 'type' => 'trust'];
        } elseif ($confirmedCount >= 1) {
            $badges[] = ['label' => 'Real-Life Confirmed', 'icon' => '👥', 'type' => 'trust'];
        }

        // if ($confirmedCount >= 1) {
        //     $badges[] = ['label' => 'Real-Life Confirmed', 'icon' => '👥'];
        // }
        // if ($confirmedCount >= 5) {
        //     $badges[] = ['label' => 'Highly Trusted (5+)', 'icon' => '🛡️'];
        // } elseif ($confirmedCount >= 3) {
        //     $badges[] = ['label' => 'Trusted (3+)', 'icon' => '🔰'];
        // }

        return view('livewire.profile.badges', [
            'badges' => $badges,
        ]);
    }
}
