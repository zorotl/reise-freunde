<?php

namespace App\Livewire\Parts;

use Livewire\Component;
use Illuminate\Support\Carbon;
use App\Models\Post; // Import Post model
use Monarobase\CountryList\CountryListFacade as Countries;

class PostCardSection extends Component
{
    // Property to receive a single Post model instance from the FeedSection component
    public Post $post;
    public string $show;
    public Carbon $now;

    public array $countryList = [];

    public function mount()
    {
        $this->now = Carbon::now();
        $this->countryList = Countries::getList('en', 'php');
    }

    public function redirectToCorrectPage()
    {
        if ($this->show == 'feed') {
            return $this->redirect('/', navigate: true);
        } elseif ($this->show == 'my') {
            return $this->redirect('/post/myown', navigate: true);
        } elseif ($this->show == 'all') {
            return $this->redirect('/post/show', navigate: true);
        } else {
            return $this->redirect('/', navigate: true);
        }
    }

    public function toggleActive(Post $post)
    {
        $post->update(['is_active' => !$post->is_active]);
        $this->redirectToCorrectPage();
    }

    public function deleteEntry(Post $post)
    {
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

    // Add methods here if this component needs actions related to a single post
    // (e.g., 'likePost', 'reportPost', 'showComments').
}