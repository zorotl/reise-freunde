<?php

namespace App\Observers;

use App\Models\UserGrant;
use App\Models\BanHistory; // Import BanHistory
use Illuminate\Support\Facades\Auth; // Import Auth facade

class UserGrantObserver
{
    /**
     * Handle the UserGrant "updated" event.
     */
    public function updated(UserGrant $userGrant): void
    {
        // Check if the 'is_banned' field was changed AND its new value is true
        if ($userGrant->isDirty('is_banned') && $userGrant->is_banned === true) {
            // Check if the user is currently authenticated (the admin performing the action)
            $adminUserId = Auth::id();

            BanHistory::create([
                'user_id' => $userGrant->user_id,
                'banned_by' => $adminUserId, // Log the admin who made the change
                'reason' => $userGrant->banned_reason, // Get reason from the grant
                'expires_at' => $userGrant->is_banned_until, // Get expiry from the grant
                'banned_at' => now(), // Record the time the ban was applied
            ]);
        }

        // Optional: You could also log when a ban is lifted (`is_banned` changes to false)
        // if ($userGrant->isDirty('is_banned') && $userGrant->is_banned === false) {
        //     // Potentially update the last ban history record or create a new 'unbanned' log entry
        // }
    }

    // Add other observer methods (created, deleted, etc.) if needed, otherwise leave empty
}