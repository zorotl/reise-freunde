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

        // Placeholder for Bürgschaften (Phase 6)

        return view('livewire.profile.badges', [
            'badges' => $badges,
        ]);
    }
}
