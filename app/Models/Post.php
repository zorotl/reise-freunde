<?php

namespace App\Models;

use App\Models\User;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'expiry_date',
        'is_active',
        'from_date',
        'to_date',
        'country',
        'city',
        'language_code',
    ];

    protected $casts = [
        'expiry_date' => 'datetime',
        'is_active' => 'boolean',
        'from_date' => 'datetime',
        'to_date' => 'datetime',
    ];

    /**
     * Check if the post is liked by a specific user.
     * Loads the relationship only if needed.
     */
    public function isLikedBy(?User $user): bool
    {
        // Return false if no user is provided (guest)
        if (!$user) {
            return false;
        }
        // Check if the 'likes' relationship is already loaded and contains the user
        // This avoids an extra query if the relationship is eager loaded.
        if ($this->relationLoaded('likes')) {
            return $this->likes->contains($user);
        }
        // If not loaded, perform an efficient query to check existence.
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // public function reports(): HasMany
    // {
    //     return $this->hasMany(PostReport::class);
    // }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    /**
     * The users that liked the post.
     */
    public function likes(): BelongsToMany
    {
        // Define the many-to-many relationship with User through the 'post_likes' table
        return $this->belongsToMany(User::class, 'post_likes', 'post_id', 'user_id')->withTimestamps();
    }

    public function language()
    {
        return $this->belongsTo(Language::class, 'language_code', 'code');
    }

}
