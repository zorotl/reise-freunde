<?php

namespace App\Livewire\Parts;

use Illuminate\Support\Collection; // Import Collection
use Livewire\Component;

class FeedSection extends Component
{
    // Property to receive the collection of posts from the parent component (Overview)
    public Collection $feedPosts;

    /**
     * Render the component view.
     */
    public function render()
    {
        // The view will loop through the $feedPosts collection and render PostCardSection
        return view('livewire.parts.feed-section');
    }

    // Add methods here if this component needs actions related to the feed container
    // (e.g., 'loadMorePosts' for pagination).
}