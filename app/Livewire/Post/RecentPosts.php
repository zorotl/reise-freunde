<?php

namespace App\Livewire\Post; // Updated namespace

use App\Models\Post;
use Livewire\Component;

class RecentPosts extends Component
{
    public $posts;

    public function mount()
    {
        $this->posts = Post::latest()->take(3)->get(); // Fetch the 3 latest posts
    }

    public function render()
    {
        return view('livewire.post.recent-posts'); // Updated view path
    }
}