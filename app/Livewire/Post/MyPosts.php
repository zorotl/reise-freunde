<?php

namespace App\Livewire\Post;

use Livewire\Component;
use App\Models\Post;
use Illuminate\Support\Carbon;

class MyPosts extends Component
{
    public $newTitle;
    public $newEntry;
    public $expiryDate;
    public $entries;
    public Carbon $now;
    public string $show = 'my';

    public function mount()
    {
        $this->now = Carbon::now();
        $this->loadMyEntries();
    }

    public function toggleActive(Post $entry)
    {
        $entry->update(['is_active' => !$entry->is_active]);
        $this->loadMyEntries();
    }

    public function deleteEntry(Post $entry)
    {
        $entry->delete(); // Soft delete
        $this->loadMyEntries();
    }

    private function loadMyEntries()
    {
        $this->entries = Post::query()
            ->where('user_id', auth()->id())
            ->latest()
            ->get();
    }
    public function render()
    {
        return view('livewire.post.post-list');
    }
}
