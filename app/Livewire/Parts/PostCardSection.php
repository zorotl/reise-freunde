<?php

namespace App\Livewire\Parts;

use App\Models\Post;
use Livewire\Component;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth; // <-- Add Auth facade
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // <-- Add this trait
use Monarobase\CountryList\CountryListFacade as Countries;

class PostCardSection extends Component
{
    use AuthorizesRequests;

    // Property to receive a single Post model instance from the FeedSection component
    public Post $post;
    public string $show;
    public Carbon $now;
    public array $countryList = [];
    public bool $isLiked; // Track like status for the current user
    public int $likesCount; // Track like count

    public function mount()
    {
        $this->now = Carbon::now();
        $this->countryList = Countries::getList('en', 'php');

        // Eager load counts and check like status on mount for initial display
        $this->post->loadCount('likes'); // Ensure likes_count is loaded
        $this->likesCount = $this->post->likes_count;
        $this->isLiked = $this->post->isLikedBy(Auth::user());
    }

    // Method to toggle the like status for the current authenticated user
    public function toggleLike()
    {
        // Ensure the user is authenticated
        if (!Auth::check()) {
            // Redirect to login or show a message
            return $this->redirect(route('login'), navigate: true);
            // Or: session()->flash('error', 'You must be logged in to like posts.'); return;
        }

        $user = Auth::user();

        // Optional: Prevent users from liking their own posts
        // if ($this->post->user_id === $user->id) {
        //     $this->dispatch('notify', ['message' => 'You cannot like your own post.', 'type' => 'warning']);
        //     return;
        // }

        // Toggle the like status in the database using the relationship method
        $user->likedPosts()->toggle($this->post->id);

        // Refresh the post data to get the updated count and status
        $this->post->refresh();
        $this->post->loadCount('likes'); // Reload the count explicitly

        // Update the component properties for immediate UI feedback
        $this->likesCount = $this->post->likes_count;
        $this->isLiked = $this->post->isLikedBy($user); // Re-check like status

        // Optionally dispatch an event if other components need to know
        // $this->dispatch('post-liked-updated', $this->post->id, $this->likesCount, $this->isLiked);
    }

    public function redirectToCorrectPage()
    {
        if ($this->show == 'feed') {
            return $this->redirect('/dashboard', navigate: true);
        } elseif ($this->show == 'my') {
            return $this->redirect('/post/myown', navigate: true);
        } elseif ($this->show == 'all') {
            return $this->redirect('/post/show', navigate: true);
        } elseif ($this->show == 'one') {
            if (Post::find($this->post->id)) {
                return $this->redirect('/post/' . $this->post->id, navigate: true);
            } else {
                return $this->redirect('/post/myown', navigate: true);
            }
        } elseif ($this->show == 'admin') {
            return $this->redirect('/admin/posts', navigate: true);
        } else {
            return $this->redirect('/dashboard', navigate: true);
        }
    }

    public function toggleActive(Post $post)
    {
        $this->authorize('toggleActive', $post); // Authorization check using the policy
        $post->update(['is_active' => !$post->is_active]);
        $this->redirectToCorrectPage();
    }

    public function deleteEntry(Post $post)
    {
        $this->authorize('delete', $post); // Authorization check using the policy
        $post->delete(); // Soft delete
        $this->redirectToCorrectPage();
    }

    /**
     * Render the component view.
     */
    public function render()
    {
        // The view will display details of the single $post object
        return view('livewire.parts.post-card-section');
    }
}