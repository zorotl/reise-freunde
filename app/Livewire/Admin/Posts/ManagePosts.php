<?php

namespace App\Livewire\Admin\Posts;

use App\Models\Post; // Import the Post model
use Livewire\Component;
use Livewire\WithPagination; // Trait for pagination

class ManagePosts extends Component
{
    use WithPagination; // Use the pagination trait

    public $search = ''; // Property for search input (title, content, maybe user name)
    public $sortField = 'created_at'; // Default sort field
    public $sortDirection = 'desc'; // Default sort direction
    public $perPage = 10; // Number of items per page

    // Listeners for events (for refreshing after actions)
    protected $listeners = [
        'postUpdated' => '$refresh', // Refresh the list when a post is updated
        'postDeleted' => '$refresh', // Refresh the list when a post is deleted
        'postRestored' => '$refresh', // Refresh the list when a post is restored
    ];


    // Reset pagination when search changes
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Set the sort field and direction
    public function sortBy($field)
    {
        // If sorting by the same field, reverse the direction
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            // Otherwise, set the new field and default to ascending
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }


    // Render the component
    public function render()
    {
        // Fetch posts, eager load the user relationship
        $posts = Post::with('user')
            ->when($this->search, function ($query) {
                // Apply search filter to title, content, or related user's name/email
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('content', 'like', '%' . $this->search . '%')
                    // Search on user's name or email
                    ->orWhereHas('user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            // Include soft deleted posts
            ->withTrashed()
            ->orderBy($this->sortField, $this->sortDirection) // Apply sorting
            ->paginate($this->perPage); // Apply pagination

        return view('livewire.admin.posts.manage-posts', [
            'posts' => $posts,
        ]);
    }

    // --- Action Methods ---

    // Method to soft delete a post
    public function softDeletePost($postId)
    {
        $post = Post::find($postId);
        if ($post) {
            $post->delete(); // Performs soft delete due to SoftDeletes trait
            session()->flash('message', 'Post soft deleted successfully.');
            $this->dispatch('postDeleted'); // Dispatch event to refresh list
        }
    }

    // Method to restore a soft deleted post
    public function restorePost($postId)
    {
        $post = Post::withTrashed()->find($postId); // Find post including soft deleted ones
        if ($post) {
            $post->restore(); // Restore the post
            session()->flash('message', 'Post restored successfully.');
            $this->dispatch('postRestored'); // Dispatch event to refresh list
        }
    }

    // Method to force delete a post
    public function forceDeletePost($postId)
    {
        $post = Post::withTrashed()->find($postId); // Find post including soft deleted ones
        if ($post) {
            $post->forceDelete(); // Permanently delete the post
            session()->flash('message', 'Post permanently deleted.');
            $this->dispatch('postDeleted'); // Dispatch event to refresh list
        }
    }

    // Edit functionality will be added in the next step, likely using a modal
}