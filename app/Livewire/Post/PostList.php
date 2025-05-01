<?php

namespace App\Livewire\Post;

use Livewire\Component;
use App\Models\Post;
use Illuminate\Support\Carbon;
use Monarobase\CountryList\CountryListFacade as Countries;
use Livewire\WithPagination;

class PostList extends Component
{
    // use WithPagination; // <-- Add if using pagination

    public $newTitle;
    public $newEntry;
    public $expiryDate;
    public $entries;
    public Carbon $now;
    public string $show = 'all';
    public array $countryList = [];

    public function mount()
    {
        $this->now = Carbon::now();
        $this->countryList = Countries::getList('en', 'php');
        $this->loadActiveEntries();
    }
    private function loadActiveEntries()
    {
        // Ensure user relationship is loaded if needed elsewhere
        $this->entries = Post::query()
            ->with('user.additionalInfo') // Eager load user->additionalInfo
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expiry_date')
                    ->orWhere('expiry_date', '>', $this->now);
            })
            ->latest()
            ->get(); // Or ->paginate(10) if using pagination
    }
    public function render()
    {
        return view('livewire.post.post-list');
    }
}
