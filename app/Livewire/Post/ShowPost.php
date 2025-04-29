<?php

namespace App\Livewire\Post;

use App\Models\Post;
use Livewire\Component;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Monarobase\CountryList\CountryListFacade as Countries;

class ShowPost extends Component
{
    public Post $post;
    public Carbon $now;
    public string $show = 'all';
    public array $countryList = [];

    public function mount(Post $post)
    {
        $this->now = Carbon::now();
        $this->post = $post->load('user.additionalInfo');
        $this->countryList = Countries::getList('en', 'php');
    }

    public function toggleActive(int $postId)
    {
        if (Auth::id() === $this->post->user_id) {
            $post = Post::findOrFail($postId);
            $post->is_active = !$post->is_active;
            $post->save();
            $this->post = $post->fresh(); // Refresh the component's post data
        } else {
            // Optionally, you can add a notification that the user is not authorized
            session()->flash('error', 'You are not authorized to perform this action.');
        }
    }

    public function deleteEntry(int $postId)
    {
        if (Auth::id() === $this->post->user_id) {
            $post = Post::findOrFail($postId);
            $post->delete();
            // Optionally, you can redirect the user back to the post list
            return redirect()->route('dashboard'); // Adjust route as needed
        } else {
            // Optionally, you can add a notification that the user is not authorized
            session()->flash('error', 'You are not authorized to perform this action.');
        }
    }

    public function render()
    {
        return view('livewire.post.show-post');
    }
}