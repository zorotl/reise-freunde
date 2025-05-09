<?php

namespace App\Livewire\User;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Monarobase\CountryList\CountryListFacade as Countries;
use Livewire\Attributes\Title;

#[Title('User Profile')]
class UserProfile extends Component
{
    public User $user;
    public ?User $loggedInUser; // Nullable for guests
    public array $countryList = [];

    // Computed properties for cleaner view logic
    #[Computed]
    public function isOwnProfile(): bool
    {
        return $this->loggedInUser && $this->loggedInUser->id === $this->user->id;
    }

    #[Computed]
    public function isFollowing(): bool
    {
        return $this->loggedInUser && $this->loggedInUser->isFollowing($this->user);
    }

    #[Computed]
    public function hasSentFollowRequest(): bool
    {
        return $this->loggedInUser && $this->loggedInUser->hasSentFollowRequestTo($this->user);
    }

    #[Computed]
    public function hasPendingFollowRequestFrom(): bool
    {
        return $this->loggedInUser && $this->loggedInUser->hasPendingFollowRequestFrom($this->user);
    }

    #[Computed]
    public function canInteract(): bool
    {
        return $this->loggedInUser && !$this->isOwnProfile;
    }

    /**
     * Determine if the logged-in user can view sensitive info
     * (e.g., counts on private profiles).
     */
    #[Computed]
    public function canViewSensitiveInfo(): bool
    {
        // Can view if it's their own profile,
        // or if the profile is not private,
        // or if they are following the private profile.
        return $this->isOwnProfile || !$this->user->isPrivate() || $this->isFollowing;
    }

    public function mount(int $id)
    {
        // Eager load relationships needed on the profile page + follow status checks
        $this->user = User::with(['additionalInfo', 'followers', 'following'])
            ->withCount(['followers', 'following']) // Get counts efficiently
            ->findOrFail($id);
        $this->loggedInUser = Auth::user();

        // Eager load relationships for the logged-in user relevant for interaction checks
        if ($this->loggedInUser) {
            $this->loggedInUser->load(['following', 'pendingFollowingRequests']);
        }

        // <-- Add this line to load the country list -->
        $this->countryList = Countries::getList('en', 'php'); // Use 'php' format for key=>value
    }

    // --- Actions ---

    public function follow()
    {
        if (!$this->canInteract)
            return;

        $this->loggedInUser->follow($this->user);
        // Refresh data after action - consider more targeted refresh if needed
        $this->refreshData();
        // TODO: Potentially dispatch browser event for optimistic UI update with Alpine.js
    }

    public function unfollow()
    {
        if (!$this->canInteract)
            return;

        $this->loggedInUser->unfollow($this->user);
        $this->refreshData();
    }

    public function cancelFollowRequest()
    {
        // Unfollowing handles cancelling requests as well
        $this->unfollow();
    }

    public function acceptFollowRequest()
    {
        if (!$this->loggedInUser || !$this->isOwnProfile)
            return; // Should not happen via UI but safety check

        // We need the user who sent the request. The button context is tricky here.
        // It's better to handle accept/decline on a dedicated requests list.
        // If you MUST have it here, you'd need to know WHOSE request you're accepting,
        // maybe passing the requesting user ID to this method.
        // Let's assume this button won't exist directly on the profile view for now.
        session()->flash('error', 'Accept/Decline should be handled on the requests page.');
    }

    public function declineFollowRequest()
    {
        if (!$this->loggedInUser || !$this->isOwnProfile)
            return;
        session()->flash('error', 'Accept/Decline should be handled on the requests page.');
    }


    // Helper to refresh user data and computed properties
    private function refreshData()
    {
        $this->user->refresh()->loadCount(['followers', 'following']);
        if ($this->loggedInUser) {
            // Reload specific relationships that might have changed
            $this->loggedInUser->load(['following', 'pendingFollowingRequests']);
            // Reset computed properties by unsetting them (Livewire recomputes on next access)
            unset($this->isFollowing);
            unset($this->hasSentFollowRequest);
            unset($this->hasPendingFollowRequestFrom);
        }
    }


    public function render()
    {
        return view('livewire.user.user-profile');
    }
}