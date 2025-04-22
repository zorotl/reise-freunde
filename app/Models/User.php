<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Notifications\FollowRequestNotification;
use App\Notifications\NewFollowerNotification;
use App\Notifications\FollowRequestAcceptedNotification;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn(string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }


    /**
     * Users that this user is following (accepted).
     */
    public function following(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_follower', 'user_id', 'following_user_id')
            ->whereNotNull('accepted_at') // Only accepted follows
            ->withTimestamps(); // Include created_at/updated_at from pivot if needed
    }

    /**
     * Users that are following this user (accepted).
     */
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_follower', 'following_user_id', 'user_id')
            ->whereNotNull('accepted_at') // Only accepted followers
            ->withTimestamps();
    }

    /**
     * Users this user has requested to follow (pending).
     */
    public function pendingFollowingRequests(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_follower', 'user_id', 'following_user_id')
            ->whereNull('accepted_at') // Only pending requests sent BY this user
            ->withTimestamps();
    }

    /**
     * Users who have requested to follow this user (pending).
     */
    public function pendingFollowerRequests(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_follower', 'following_user_id', 'user_id')
            ->whereNull('accepted_at') // Only pending requests sent TO this user
            ->withTimestamps();
    }

    // --- Helper Methods ---

    /**
     * Check if the current user is actively following the given user.
     */
    public function isFollowing(User $user): bool
    {
        if ($this->relationLoaded('following')) {
            return $this->following->contains($user);
        }
        return $this->following()
            ->where('user_follower.following_user_id', $user->id)
            ->exists();
    }

    /**
     * Check if the current user has a pending follow request sent to the given user.
     */
    public function hasSentFollowRequestTo(User $user): bool
    {
        if ($this->relationLoaded('pendingFollowingRequests')) {
            return $this->pendingFollowingRequests->contains($user);
        }
        // Check the pivot table directly for a pending request from this user to the target user
        return \Illuminate\Support\Facades\DB::table('user_follower')
            ->where('user_id', $this->id)
            ->where('following_user_id', $user->id)
            ->whereNull('accepted_at')
            ->exists();
    }

    /**
     * Check if the given user has a pending follow request sent to the current user.
     */
    public function hasPendingFollowRequestFrom(User $user): bool
    {
        if ($this->relationLoaded('pendingFollowerRequests')) {
            return $this->pendingFollowerRequests->contains($user);
        }
        // Check the pivot table directly for a pending request from the target user to this user
        return \Illuminate\Support\Facades\DB::table('user_follower')
            ->where('user_id', $user->id)
            ->where('following_user_id', $this->id)
            ->whereNull('accepted_at')
            ->exists();
    }

    /**
     * Check if the user's profile is private.
     * Ensure additionalInfo relationship is loaded for efficiency if checking multiple users.
     */
    public function isPrivate(): bool
    {
        // Eager load 'additionalInfo' if you check this often in loops
        return $this->additionalInfo?->is_private ?? false;
    }

    /**
     * Get the pivot record for a follow request sent TO this user FROM the specified user.
     */
    private function getFollowRequestPivot(User $requester)
    {
        return \Illuminate\Support\Facades\DB::table('user_follower')
            ->where('user_id', $requester->id)
            ->where('following_user_id', $this->id)
            ->first();
    }

    /**
     * Accept a follow request from another user.
     */
    public function acceptFollowRequest(User $requester): bool
    {
        $accepted = \Illuminate\Support\Facades\DB::table('user_follower')
            ->where('user_id', $requester->id)
            ->where('following_user_id', $this->id)
            ->whereNull('accepted_at') // Ensure it's a pending request
            ->update(['accepted_at' => now()]);

        if ($accepted > 0) {
            // Send notification to the requester that their request was accepted
            $requester->notify(new FollowRequestAcceptedNotification($this)); // <-- Uncommented and adjusted
        }

        return $accepted > 0;
    }

    /**
     * Decline (or cancel) a follow request from another user.
     */
    public function declineFollowRequest(User $requester): bool
    {
        $deleted = \Illuminate\Support\Facades\DB::table('user_follower')
            ->where('user_id', $requester->id)
            ->where('following_user_id', $this->id)
            // ->whereNull('accepted_at') // Remove this line if you want to allow removing accepted followers too
            ->delete();

        return $deleted > 0;
    }

    /**
     * Send a follow request or directly follow a user.
     */
    public function follow(User $userToFollow): void
    {
        if ($this->id === $userToFollow->id || $this->isFollowing($userToFollow) || $this->hasSentFollowRequestTo($userToFollow)) {
            return; // Cannot follow self, already following, or request already sent
        }

        $isPrivate = $userToFollow->isPrivate();
        $this->pendingFollowingRequests()->attach($userToFollow->id, [
            'accepted_at' => $isPrivate ? null : now()
        ]);

        if ($isPrivate) {
            // Send FollowRequestNotification to the user being followed
            $userToFollow->notify(new FollowRequestNotification($this)); // Pass the current user (follower)
        } else {
            // Send NewFollowerNotification to the user being followed
            $userToFollow->notify(new NewFollowerNotification($this)); // Pass the current user (follower)
        }
    }

    /**
     * Unfollow a user or cancel a pending request.
     */
    public function unfollow(User $userToUnfollow): void
    {
        // Detach from both potential relationships (accepted follow or pending request)
        $this->following()->detach($userToUnfollow->id);
        $this->pendingFollowingRequests()->detach($userToUnfollow->id); // This covers cancelling requests
    }









    /**
     * Relationships
     */

    public function post()
    {
        return $this->hasMany(Post::class, 'user_id');
    }

    public function grant()
    {
        return $this->hasOne(UserGrant::class, 'user_id');
    }

    public function additionalInfo()
    {
        return $this->hasOne(UserAdditionalInfo::class, 'user_id');
    }

    public function travelStyles()
    {
        return $this->belongsToMany(TravelStyle::class);
    }

    public function hobbies()
    {
        return $this->belongsToMany(Hobby::class);
    }
}
