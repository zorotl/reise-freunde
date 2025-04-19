<?php

namespace App\Livewire\Post;

use Livewire\Component;
use App\Models\Post;
use Illuminate\Support\Carbon;

class PostList extends Component
{
    public $newTitle;
    public $newEntry;
    public $expiryDate;
    public $entries;
    public Carbon $now;
    public string $show = 'all';

    public function mount()
    {
        $this->now = Carbon::now();
        $this->loadActiveEntries();
    }

    public function toggleActive(Post $entry)
    {
        $entry->update(['is_active' => !$entry->is_active]);
        $this->loadActiveEntries();
    }

    public function deleteEntry(Post $entry)
    {
        $entry->delete(); // Soft delete
        $this->loadActiveEntries();
    }

    private function loadActiveEntries()
    {
        $this->entries = Post::query()
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expiry_date')
                    ->orWhere('expiry_date', '>', $this->now);
            })
            ->latest()
            ->get();
    }
    public function render()
    {
        return view('livewire.post.post-list');
    }
}
