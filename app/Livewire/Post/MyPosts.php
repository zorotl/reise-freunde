<?php

namespace App\Livewire\Post;

use Livewire\Component;
use App\Models\Post;
use Illuminate\Support\Carbon;
use Monarobase\CountryList\CountryListFacade as Countries;
use Livewire\WithPagination;

class MyPosts extends Component
{
    use WithPagination; // <-- Add if using pagination

    public Carbon $now;
    public string $show = 'my';
    public array $countryList = [];

    public function mount()
    {
        $this->now = Carbon::now();
        $this->countryList = Countries::getList('en', 'php');
    }

    public function render()
    {
        // Fetch paginated entries directly in the render method
        $entries = Post::query()
            ->with('user.additionalInfo') // Eager load user->additionalInfo
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10); // <-- Use paginate() instead of get(), choose items per page (e.g., 10)

        // Pass the paginated collection to the view
        return view('livewire.post.post-list', [
            'entries' => $entries
        ]);
    }
}
