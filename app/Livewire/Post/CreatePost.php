<?php

namespace App\Livewire\Post;

use Livewire\Component;
use App\Models\Post;

class CreatePost extends Component
{
    public $title;
    public $content;
    public $expiryDate;
    public $entries;

    public function save()
    {
        $this->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'expiryDate' => 'required|date|after:today',
        ]);

        Post::create([
            'user_id' => auth()->id(),
            'title' => $this->title,
            'content' => $this->content,
            'expiry_date' => $this->expiryDate,
        ]);

        session()->flash('success', 'New post successfully created.');
        $this->redirect('/post/show', navigate: true);
    }
    public function render()
    {
        return view('livewire.post.create-post');
    }
}
