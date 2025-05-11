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

        // Status badge
        if ($this->user->status === 'approved') {
            $badges[] = ['label' => 'Verified by Admin', 'icon' => '✅'];
        } elseif ($this->user->status === 'auto-approved') {
            $badges[] = ['label' => 'Auto Verified', 'icon' => '⏱️'];
        }

        // Verification data
        $verification = $this->user->verification;
        if ($verification) {
            if ($verification->id_document_path) {
                $badges[] = ['label' => 'ID Verified', 'icon' => '🪪'];
            }
            if (!empty($verification->social_links)) {
                $badges[] = ['label' => 'Social Linked', 'icon' => '🌐'];
            }
        }

        // Bürgschaften
        $confirmedCount = \App\Models\UserConfirmation::where(function ($q) {
            $q->where('requester_id', $this->user->id)
                ->orWhere('confirmer_id', $this->user->id);
        })
            ->where('status', 'accepted')->count();

        if ($confirmedCount > 0) {
            $badges[] = ['label' => 'Real-Life Confirmed', 'icon' => '👥'];
        }

        return view('livewire.profile.badges', [
            'badges' => $badges,
        ]);
    }
}
