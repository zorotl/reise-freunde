<?php

namespace App\Livewire\Post;

use App\Models\Post;
use Livewire\Component;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Monarobase\CountryList\CountryListFacade as Countries;
use Livewire\Attributes\Title;

#[Title('Show Post')]
class ShowPost extends Component
{
    public Post $post;
    public Carbon $now;
    public string $show = 'one';
    public array $countryList = [];

    public function mount(Post $post)
    {
        $this->now = Carbon::now();
        $this->post = $post->loadCount('likes')->load('user.additionalInfo', 'language');
        $this->countryList = Countries::getList('en', 'php');
    }


    public function render()
    {
        return view('livewire.post.show-post');
    }
}