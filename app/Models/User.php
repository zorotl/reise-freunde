<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Notifications\NewFollowerNotification;
use App\Notifications\FollowRequestNotification;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\FollowRequestAcceptedNotification;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable // Add MustVerifyEmail if you implement it later
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        // 'name', // Remove name
        'firstname', // Add firstname
        'lastname', // Add lastname
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
     * Define an accessor for the 'name' attribute.
     * This combines firstname and lastname for backward compatibility.
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => ($attributes['firstname'] ?? '') . ' ' . ($attributes['lastname'] ?? ''),
        );
    }


    // Added accessor to check if user has admin or moderator role
    public function isAdminOrModerator(): bool
    {
        // Load the grant relationship if it hasn't been loaded yet
        if (!$this->relationLoaded('grant')) {
            $this->load('grant');
        }

        // Check if the user has a grant and if they are admin or moderator
        return $this->grant && ($this->grant->is_admin || $this->grant->is_moderator);
    }

    /**
     * Get the user's initials
     * Updated to use firstname and lastname
     */
    public function initials(): string
    {
        $firstInitial = Str::of($this->firstname ?? '')->substr(0, 1);
        $lastInitial = Str::of($this->lastname ?? '')->substr(0, 1);
        return $firstInitial . $lastInitial;
    }

    /**
     * Get the URL to the user's profile picture or a default avatar.
     */
    public function profilePictureUrl(): string
    {
        $defaultAvatar = asset('images/default-avatar.png');

        if (
            $this->additionalInfo &&
            $this->additionalInfo->profile_picture_path &&
            Storage::disk('public')->exists($this->additionalInfo->profile_picture_path)
        ) {
            return asset('storage/' . $this->additionalInfo->profile_picture_path);
        }

        return $defaultAvatar;
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

    public function posts() // Corrected relationship name (plural)
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

    /**
     * Get the messages sent by the user.
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Get the messages received by the user.
     */
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    // Reports submitted BY this user
    public function submittedReports(): HasMany
    {
        return $this->hasMany(PostReport::class, 'user_id');
    }

    // Reports processed BY this user (if admin/mod)
    public function processedReports(): HasMany
    {
        return $this->hasMany(PostReport::class, 'processed_by');
    }

    /**
     * The posts that the user has liked.
     */
    public function likedPosts(): BelongsToMany
    {
        // Define the many-to-many relationship with Post through the 'post_likes' table
        return $this->belongsToMany(Post::class, 'post_likes', 'user_id', 'post_id')->withTimestamps();
    }
}