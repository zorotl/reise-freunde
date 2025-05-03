<?php

namespace App\Livewire\Post;

use Livewire\Component;
use App\Models\Post;
use Carbon\Carbon;
use Monarobase\CountryList\CountryListFacade as Countries;
use Illuminate\Validation\Rule;

class CreatePost extends Component
{
    public $title;
    public $content;
    public $expiryDate;
    public $fromDate;
    public $toDate;
    public $country;
    public $city;
    public $origin = 'all';
    public $action = 'save';
    public $buttonText = 'Create Post';
    public array $countryList = [];

    public function mount()
    {
        $this->countryList = Countries::getList('en', 'php');
    }
    public function save()
    {
        $this->validate([
            'title' => 'required|min:3|max:255',
            'content' => 'required|min:50',
            'expiryDate' => 'required|date|after:today|before_or_equal:+2 years|before_or_equal:fromDate',
            'fromDate' => 'required|date|after:today|before_or_equal:+1 years|before:toDate',
            'toDate' => 'required|date|after:today|before_or_equal:+2 years|after:fromDate',
            'country' => [
                'nullable',
                'string',
                'size:2',
                Rule::in(array_keys($this->countryList)) // Validate against fetched country codes
            ],
            'city' => 'nullable|string|max:255',
        ]);

        Post::create([
            'user_id' => auth()->id(),
            'title' => $this->title,
            'content' => $this->content,
            'expiry_date' => $this->expiryDate,
            'from_date' => $this->fromDate,
            'to_date' => $this->toDate,
            'country' => $this->country,
            'city' => $this->city,
        ]);

        session()->flash('success', 'New post successfully created.');
        $this->redirect('/post/show', navigate: true);
    }
    public function render()
    {
        return view('livewire.post.form-post', [
            'action' => $this->action,
            'buttonText' => $this->buttonText,
        ]);
    }
}
