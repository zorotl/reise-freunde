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

    public function mount()
    {
        $this->now = Carbon::now();
        $this->countryList = Countries::getList('en', 'php');
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
        // Authorization check using the policy
        $this->authorize('toggleActive', $post);

        $post->update(['is_active' => !$post->is_active]);
        $this->redirectToCorrectPage();
    }

    public function deleteEntry(Post $post)
    {
        // Authorization check using the policy
        $this->authorize('delete', $post);

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