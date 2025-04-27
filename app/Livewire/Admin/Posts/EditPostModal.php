<?php

namespace App\Livewire\Admin\Posts;

use App\Models\Post;
use Livewire\Component;
use Livewire\Attributes\On;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Monarobase\CountryList\CountryListFacade as Countries;

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
    public array $countryList = [];

    // Define validation rules
    protected $baseRules = [
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'expiry_date' => 'required|date|after_or_equal:today',
        'is_active' => 'boolean',
        'from_date' => 'required|date',
        'to_date' => 'required|date|after_or_equal:from_date',
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

        // --- Load Country List ---
        $this->countryList = Countries::getList('en', 'php');

        // Populate properties with post data
        $this->postId = $post->id;
        $this->title = $post->title;
        $this->content = $post->content;
        // Format dates for input fields (typically YYYY-MM-DD or YYYY-MM-DDTHH:MM)
        $this->expiry_date = $post->expiry_date ? $post->expiry_date->format('Y-m-d') : null;
        $this->is_active = $post->is_active;
        $this->from_date = $post->from_date ? $post->from_date->format('Y-m-d') : null;
        $this->to_date = $post->to_date ? $post->to_date->format('Y-m-d') : null;
        $this->country = $post->country; // Assign the country code
        $this->city = $post->city;


        $this->show = true; // Show the modal
    }

    // Save the post changes
    public function savePost()
    {
        // --- Define dynamic rules HERE ---
        $rules = $this->baseRules; // Start with base rules
        $rules['country'] = [         // Add the dynamic country rule
            'nullable',
            'string',
            'size:2',
            Rule::in(array_keys($this->countryList)) // Now $this->countryList is available
        ];

        // Validate using the dynamically built rules array
        $validatedData = $this->validate($rules);

        $post = Post::find($this->postId);

        if (!$post) {
            session()->flash('error', 'Post not found.');
            $this->closeModal();
            return;
        }

        // Update post properties using validated data
        $post->update($validatedData); // Use mass assignment with validated data

        // Update post properties
        // $post->title = $this->title;
        // $post->content = $this->content;
        // $post->expiry_date = $this->expiry_date;
        // $post->is_active = $this->is_active;
        // $post->from_date = $this->from_date;
        // $post->to_date = $this->to_date;
        // $post->country = $this->country;
        // $post->city = $this->city;

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
            'countryList',
        ]); // Reset all properties
        $this->resetValidation(); // Clear validation errors
    }

    // Render the modal view
    public function render()
    {
        return view('livewire.admin.posts.edit-post-modal');
    }
}