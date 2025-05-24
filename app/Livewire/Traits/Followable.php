<?php

namespace App\Livewire\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

trait Followable
{
    /**
     * Follow or send a follow request to a user.
     */
    public function followUser(int $userId): void
    {
        $userToFollow = User::findOrFail($userId);
        $currentUser = Auth::user();

        if ($currentUser->isFollowing($userToFollow) || $currentUser->id === $userToFollow->id) {
            return; // Already following or cannot follow self
        }

        if ($userToFollow->private_profile) {
            $currentUser->sendFollowRequest($userToFollow);
            // Dispatch notification for feedback
            $this->dispatch('user-action-feedback', message: __('Follow request sent.'));
        } else {
            $currentUser->follow($userToFollow);
            // Dispatch notification for feedback
            $this->dispatch('user-action-feedback', message: __('You are now following :name.', ['name' => $userToFollow->username]));
        }

        // Dispatch an event to notify components to refresh
        $this->dispatch('userFollowStateChanged', userId: $userId);
    }

    /**
     * Unfollow a user.
     */
    public function unfollowUser(int $userId): void
    {
        $userToUnfollow = User::findOrFail($userId);
        $currentUser = Auth::user();

        if ($currentUser->isFollowing($userToUnfollow)) {
            $currentUser->unfollow($userToUnfollow);
            // Dispatch notification for feedback
            $this->dispatch('user-action-feedback', message: __('You have unfollowed :name.', ['name' => $userToUnfollow->username]));
        }

        // Dispatch an event to notify components to refresh
        $this->dispatch('userFollowStateChanged', userId: $userId);
    }

    /**
     * Cancel a follow request sent to a user.
     */
    public function cancelFollowRequest(int $userId): void
    {
        $userToCancel = User::findOrFail($userId);
        $currentUser = Auth::user();

        if ($currentUser->hasSentFollowRequestTo($userToCancel)) {
            $currentUser->cancelFollowRequest($userToCancel);
            // Dispatch notification for feedback
            $this->dispatch('user-action-feedback', message: __('Follow request cancelled.'));
        }

        // Dispatch an event to notify components to refresh
        $this->dispatch('userFollowStateChanged', userId: $userId);
    }

    /**
     * A default refresh method. Components can override this or implement their own.
     * This listener will cause a re-render when the state changes.
     */
    #[On('userFollowStateChanged')]
    public function refreshData(): void
    {
        // This simply ensures the component re-renders.
        // If more specific data reloading is needed (like reloading relations),
        // the component can implement its own listener or override this.
        // For profile and lists, we will add specific listeners.
    }

    /**
     * Listener for user action feedback to show notifications.
     * Requires x-notifications to be present in the layout.
     */
    #[On('user-action-feedback')]
    public function showFeedbackNotification(string $message): void
    {
        $this->dispatch('notify', ['message' => $message, 'type' => 'success']);
    }
}