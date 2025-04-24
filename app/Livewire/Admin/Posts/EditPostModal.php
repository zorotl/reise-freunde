<?php

namespace App\Livewire\Admin\Posts;

use App\Models\Post; // Import Post model
use Livewire\Component;
use Livewire\Attributes\On; // Trait for listeners
use Carbon\Carbon; // Import Carbon for date handling

class EditPostModal extends Component
{
    public $show = false; // Property to control modal visibility
    public $postId; // To store the ID of the post being edited

    // Properties to hold post data for the form
    public $title;
    public $content;
    public $expiry_date;
    public $is_active;
    public $from_date;
    public $to_date;
    public $country;
    public $city;

    // Define validation rules
    protected $rules = [
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'expiry_date' => 'required|date|after_or_equal:today',
        'is_active' => 'boolean',
        'from_date' => 'required|date',
        'to_date' => 'required|date|after_or_equal:from_date',
        'country' => 'nullable|string|max:255',
        'city' => 'nullable|string|max:255',
    ];

    // Listen for the openEditModal event
    #[On('openEditPostModal')] // <-- Use a distinct event name for posts
    public function openEditPostModal($postId)
    {
        $post = Post::find($postId); // Find the post

        if (!$post) {
            // Handle case where post is not found
            session()->flash('error', 'Post not found.');
            $this->closeModal();
            return;
        }

        // Populate properties with post data
        $this->postId = $post->id;
        $this->title = $post->title;
        $this->content = $post->content;
        // Format dates for input fields (typically YYYY-MM-DD or YYYY-MM-DDTHH:MM)
        $this->expiry_date = $post->expiry_date ? $post->expiry_date->format('Y-m-d') : null;
        $this->is_active = $post->is_active;
        $this->from_date = $post->from_date ? $post->from_date->format('Y-m-d') : null;
        $this->to_date = $post->to_date ? $post->to_date->format('Y-m-d') : null;
        $this->country = $post->country;
        $this->city = $post->city;


        $this->show = true; // Show the modal
    }

    // Save the post changes
    public function savePost()
    {
        $this->validate(); // Run validation

        $post = Post::find($this->postId);

        if (!$post) {
            session()->flash('error', 'Post not found.');
            $this->closeModal();
            return;
        }

        // Update post properties
        $post->title = $this->title;
        $post->content = $this->content;
        $post->expiry_date = $this->expiry_date;
        $post->is_active = $this->is_active;
        $post->from_date = $this->from_date;
        $post->to_date = $this->to_date;
        $post->country = $this->country;
        $post->city = $this->city;

        $post->save();

        session()->flash('message', 'Post updated successfully.');

        $this->dispatch('postUpdated'); // Dispatch event to refresh the post list
        $this->closeModal(); // Close the modal
    }

    // Close the modal and reset properties
    public function closeModal()
    {
        $this->show = false;
        $this->reset([
            'postId',
            'title',
            'content',
            'expiry_date',
            'is_active',
            'from_date',
            'to_date',
            'country',
            'city',
        ]); // Reset all properties
        $this->resetValidation(); // Clear validation errors
    }

    // Render the modal view
    public function render()
    {
        return view('livewire.admin.posts.edit-post-modal');
    }
}