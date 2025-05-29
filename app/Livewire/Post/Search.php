<?php

namespace App\Livewire\Post;

use App\Models\Post;
use Livewire\Component;

class Search extends Component
{
    public $query = '';
    public $results = [];

    public function updatedQuery()
    {
        if (strlen($this->query) >= 2) {
            $this->results = Post::where('title', 'like', '%' . $this->query . '%')
                ->orWhere('content', 'like', '%' . $this->query . '%')
                ->take(5) // Limit the number of results
                ->get();
        } else {
            $this->results = [];
        }
    }

    public function render()
    {
        return view('livewire.post.search');
    }
}