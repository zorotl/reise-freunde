<?php

namespace App\Livewire\Parts;

use App\Models\Post; // Import Post model
use Livewire\Component;
use Monarobase\CountryList\CountryListFacade as Countries;

class PostCardSection extends Component
{
    // Property to receive a single Post model instance from the FeedSection component
    public Post $post;
    public array $countryList = [];

    public function mount()
    {
        $this->countryList = Countries::getList('en', 'php');
    }

    /**
     * Render the component view.
     */
    public function render()
    {
        // The view will display details of the single $post object
        return view('livewire.parts.post-card-section');
    }

    // Add methods here if this component needs actions related to a single post
    // (e.g., 'likePost', 'reportPost', 'showComments').
}