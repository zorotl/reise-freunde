<?php

// namespace App\Livewire\Post;

// use Livewire\Component;
// use App\Models\Post;
// use Illuminate\Support\Carbon;

// class ShowPosts extends Component
// {
//     public $newTitle;
//     public $newEntry;
//     public $expiryDate;
//     public $entries;

//     public function mount()
//     {
//         $this->loadEntries();
//     }

//     public function toggleActive(Post $entry)
//     {
//         $entry->update(['is_active' => !$entry->is_active]);
//         $this->loadEntries();
//     }

//     public function deleteEntry(Post $entry)
//     {
//         $entry->delete(); // Soft delete
//         $this->loadEntries();
//     }

//     private function loadEntries()
//     {
//         $this->entries = Post::query()
//             ->where('is_active', true)
//             ->where(function ($query) {
//                 $query->whereNull('expiry_date')
//                     ->orWhere('expiry_date', '>', Carbon::now());
//             })
//             ->latest()
//             ->get();
//     }
//     public function render()
//     {
//         return view('livewire.post.show-posts');
//     }
// }
