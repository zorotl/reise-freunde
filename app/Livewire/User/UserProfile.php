<?php

namespace App\Livewire\User;

use App\Livewire\Traits\Followable;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Monarobase\CountryList\CountryList;

#[Title('Profile')]
class UserProfile extends Component
{
    use Followable;

    public User $user;
    public array $countryList = [];

    public function mount(int $id): void
    {
        $this->user = User::withCount(['followers', 'following', 'spokenLanguages'])->findOrFail($id);
        $countryList = new CountryList();
        $this->countryList = $countryList->getList(app()->getLocale());
    }

    #[Computed]
    public function isOwnProfile(): bool
    {
        return Auth::check() && $this->user->id === Auth::id();
    }

    #[Computed]
    public function canViewSensitiveInfo(): bool
    {
        if (!Auth::check()) {
            return false;
        }
        $authUser = Auth::user();
        if ($authUser->isAdminOrModerator()) {
            return true;
        }
        if ($this->isOwnProfile()) {
            return true;
        }
        if (!$this->user->isPrivate()) { // Using isPrivate() from User model
            return true;
        }
        return $authUser->isFollowing($this->user);
    }

    #[Computed]
    public function canInteract(): bool
    {
        if (!Auth::check() || $this->isOwnProfile()) {
            return false;
        }
        $profileUserIsNotBanned = !$this->user->banHistory()->where('status', 'active')->exists();
        $authUserIsNotBanned = !Auth::user()->banHistory()->where('status', 'active')->exists();
        return $profileUserIsNotBanned && $authUserIsNotBanned;
    }

    #[Computed]
    public function isFollowing(): bool
    {
        if (!Auth::check() || $this->isOwnProfile()) {
            return false;
        }
        return Auth::user()->isFollowing($this->user);
    }

    #[Computed]
    public function hasSentFollowRequest(): bool
    {
        if (!Auth::check() || $this->isOwnProfile()) {
            return false;
        }
        return Auth::user()->hasSentFollowRequestTo($this->user);
    }

    /**
     * Check if the profile user ($this->user) has a pending follow request
     * FROM the currently authenticated user (Auth::user()).
     * This is used, for example, on the profile page of user X,
     * when Auth::user() is viewing it, to see if X has requested to follow Auth::user().
     */
    #[Computed] // <-- ADD THIS
    public function hasPendingFollowRequestFrom(): bool
    {
        if (!Auth::check()) {
            return false;
        }
        // Check if $this->user (the profile being viewed) has a pending request FROM Auth::user()
        return $this->user->hasPendingFollowRequestFrom(Auth::user());
    }

    #[On('userFollowStateChanged')]
    public function refreshProfileUser(int $userId): void
    {
        if ($this->user->id === $userId || (Auth::check() && Auth::id() === $userId)) {
            $this->user = $this->user->fresh()->loadCount(['followers', 'following']);
            unset($this->isFollowing);
            unset($this->hasSentFollowRequest);
            unset($this->hasPendingFollowRequestFrom); // <-- Unset this too
        }
    }

    public function render()
    {
        return view('livewire.user.user-profile')
            ->layout('components.layouts.app');
    }
}