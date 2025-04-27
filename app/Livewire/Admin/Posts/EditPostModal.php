<?php

namespace App\Livewire\Admin\Posts;

use App\Models\Post;
use Livewire\Component;
use Livewire\Attributes\On;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Monarobase\CountryList\CountryListFacade as Countries;

class EditPostModal extends Component
{
    public $show = false;
    public $postId;

    // Form properties
    public $title;
    public $content;
    public $expiry_date;
    public $is_active;
    public $from_date;
    public $to_date;
    public $country;
    public $city;

    public array $countryList = []; // Keep this public property

    protected $baseRules = [
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'expiry_date' => 'required|date|after_or_equal:today',
        'is_active' => 'boolean',
        'from_date' => 'required|date',
        'to_date' => 'required|date|after_or_equal:from_date',
        'city' => 'nullable|string|max:255',
    ];

    #[On('openEditPostModal')]
    public function openEditPostModal($postId)
    {
        // Ensure countryList is populated when modal opens
        $this->countryList = Countries::getList('en', 'php');

        $post = Post::find($postId);
        if (!$post) {
            session()->flash('error', 'Post not found.');
            $this->closeModal();
            return;
        }

        // Populate properties
        $this->postId = $post->id;
        $this->title = $post->title;
        $this->content = $post->content;
        $this->expiry_date = $post->expiry_date ? $post->expiry_date->format('Y-m-d') : null;
        $this->is_active = $post->is_active;
        $this->from_date = $post->from_date ? $post->from_date->format('Y-m-d') : null;
        $this->to_date = $post->to_date ? $post->to_date->format('Y-m-d') : null;
        $this->country = $post->country;
        $this->city = $post->city;

        $this->resetValidation();
        $this->show = true;
    }

    public function savePost()
    {
        $rules = $this->baseRules;
        // Ensure countryList is loaded if validation happens before mount/open (unlikely here, but safe)
        $countries = empty($this->countryList) ? Countries::getList('en', 'php') : $this->countryList;
        $rules['country'] = [
            'nullable',
            'string',
            'size:2',
            Rule::in(array_keys($countries))
        ];

        $validatedData = $this->validate($rules);
        $post = Post::find($this->postId);
        if (!$post) { /* handle error */
            return;
        }

        $post->update($validatedData);
        session()->flash('message', 'Post updated successfully.');
        $this->dispatch('postUpdated');
        $this->closeModal();
    }

    public function closeModal()
    {
        $this->show = false;
        $this->reset([
            'postId',
            'title',
            'content',
            'expiry_date',
            'is_active',
            'from_date',
            'to_date',
            'country',
            'city',
            'countryList' // Still reset countryList
        ]);
        $this->resetValidation();
    }

    // No with() method needed here

    public function render()
    {
        return view('livewire.admin.posts.edit-post-modal');
    }
}