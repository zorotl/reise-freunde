<?php

namespace App\Livewire\Admin\Posts;

use App\Models\Post;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Monarobase\CountryList\CountryListFacade as Countries;
use Livewire\Attributes\Layout; // <-- Add Layout attribute
use Livewire\Attributes\Title; // <-- Add Title attribute

// Use the admin layout
#[Layout('components.layouts.admin')]
#[Title('Admin: Edit Post')] // Set page title
class EditPost extends Component
{
    public Post $post; // Route model binding

    // Form properties
    public ?string $title = null;
    public ?string $content = null;
    public ?string $expiryDate = null;
    public ?bool $is_active = true;
    public ?string $fromDate = null;
    public ?string $toDate = null;
    public ?string $country = null;
    public ?string $city = null;
    public array $countryList = [];

    // Validation rules (dynamic country rule handled in update method)
    protected array $baseRules = [
        'title' => 'required|string|min:3|max:255',
        'content' => 'required|string',
        'expiryDate' => 'required|date|after_or_equal:today',
        'is_active' => 'required|boolean',
        'fromDate' => 'required|date',
        'toDate' => 'required|date|after_or_equal:from_date',
        'city' => 'nullable|string|max:255',
    ];

    /**
     * Mount the component, load data, and populate the country list.
     * Route model binding automatically injects the Post model instance.
     */
    public function mount(Post $post): void
    {
        $this->post = $post; // Post is injected via route model binding
        $this->countryList = Countries::getList('en', 'php');

        // Populate form properties from the post model
        $this->title = $post->title;
        $this->content = $post->content;
        $this->expiryDate = $post->expiry_date?->format('Y-m-d');
        $this->is_active = $post->is_active;
        $this->fromDate = $post->from_date?->format('Y-m-d');
        $this->toDate = $post->to_date?->format('Y-m-d');
        $this->country = $post->country; // The country code
        $this->city = $post->city;
    }

    /**
     * Update the post.
     */
    public function update(): void
    {
        // Define dynamic rules including country validation
        $rules = $this->baseRules;
        $rules['country'] = [
            'nullable',
            'string',
            'size:2',
            Rule::in(array_keys($this->countryList))
        ];

        // Validate current component properties
        $validatedData = $this->validate($rules);

        // Update the post instance
        $this->post->update($validatedData);

        session()->flash('message', 'Post updated successfully.');

        // Redirect back to the manage posts page
        $this->redirect(route('admin.posts'), navigate: true);
    }

    /**
     * Render the component view.
     * We can potentially reuse the existing form-post view.
     */
    public function render()
    {
        // Pass necessary variables to the view if using the shared form
        return view('livewire.post.form-post', [
            'action' => 'updatePost', // Corresponds to the method name to call on submit
            'buttonText' => __('Update Post'),
            'origin' => 'admin', // Indicate origin if needed by the form view
        ]);
        // OR return view('livewire.admin.posts.edit-post'); // If you create a dedicated view
    }
}