<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization; // Import HandlesAuthorization

class PostPolicy
{
    // --- REMOVED: use HandlesAuthorization; --- // Remove this line

    /**
     * Determine whether the user can view any models.
     * Generally true for logged-in users, adjust if needed.
     */
    public function viewAny(User $user): bool
    {
        return true; // Or specific logic if needed
    }

    /**
     * Determine whether the user can view the model.
     * Generally true if the post is active or the user is the owner/admin.
     */
    public function view(User $user, Post $post): bool
    {
        // Allow viewing if the post is active, or if the user is the owner or an admin/moderator
        return $post->is_active || $user->id === $post->user_id || $user->isAdminOrModerator();
    }

    /**
     * Determine whether the user can create models.
     * Any authenticated user can create posts.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     * Only the post owner or an admin/moderator can update.
     */
    public function update(User $user, Post $post): bool
    {
        // Check if the user is the owner OR if they are an admin/moderator
        return $user->id === $post->user_id || $user->isAdminOrModerator();
    }

    /**
     * Determine whether the user can delete the model (soft delete).
     * Only the post owner or an admin/moderator can delete.
     */
    public function delete(User $user, Post $post): bool
    {
        // Check if the user is the owner OR if they are an admin/moderator
        return $user->id === $post->user_id || $user->isAdminOrModerator();
    }

    /**
     * Determine whether the user can restore the model.
     * Only admin/moderator can restore.
     */
    public function restore(User $user, Post $post): bool
    {
        // Only admin/moderator can restore
        return $user->isAdminOrModerator();
    }

    /**
     * Determine whether the user can permanently delete the model.
     * Only admin/moderator can force delete.
     */
    public function forceDelete(User $user, Post $post): bool
    {
        // Only admin/moderator can force delete
        return $user->isAdminOrModerator();
    }

    /**
     * Determine whether the user can toggle the active state.
     * Only the post owner or an admin/moderator can toggle active state.
     */
    public function toggleActive(User $user, Post $post): bool
    {
        // Check if the user is the owner OR if they are an admin/moderator
        return $user->id === $post->user_id || $user->isAdminOrModerator();
    }
}