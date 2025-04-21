<?php

namespace App\Livewire\Post;

use App\Models\Post;
use Livewire\Component;
use Illuminate\Support\Carbon;

class ShowPost extends Component
{
    public Post $post;
    public Carbon $now;

    public function mount(Post $post)
    {
        $this->now = Carbon::now();
        $this->post = $post;
    }

    public function render()
    {
        return view('livewire.post.show-post');
    }
}