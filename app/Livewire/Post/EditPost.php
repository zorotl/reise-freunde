<?php

namespace App\Livewire\Post;

use App\Models\Post;
use Livewire\Component;
use Monarobase\CountryList\CountryListFacade as Countries;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class EditPost extends Component
{
    use AuthorizesRequests;

    public Post $entry;
    public $title;
    public $content;
    public $expiryDate;
    public $fromDate;
    public $toDate;
    public $country;
    public $city;
    public $origin;
    public $action = 'update';
    public $buttonText = 'Update Post';
    public array $countryList = [];

    public function mount(Post $id)
    {
        // Authorization check on mount
        $this->authorize('update', $id);

        $this->origin = request('origin');
        $this->entry = $id;
        $this->title = $this->entry->title;
        $this->content = $this->entry->content;
        $this->expiryDate = $this->entry->expiry_date ? $this->entry->expiry_date->format('Y-m-d') : null;
        $this->fromDate = $this->entry->from_date ? $this->entry->from_date->format('Y-m-d') : null;
        $this->toDate = $this->entry->to_date ? $this->entry->to_date->format('Y-m-d') : null;
        $this->country = $this->entry->country;
        $this->city = $this->entry->city;
        $this->countryList = Countries::getList('en', 'php');
    }

    public function redirectToCorrectPage()
    {
        if ($this->origin == 'feed') {
            return $this->redirect('/dashboard', navigate: true);
        } elseif ($this->origin == 'my') {
            return $this->redirect('/post/myown', navigate: true);
        } elseif ($this->origin == 'all') {
            return $this->redirect('/post/show', navigate: true);
        } elseif ($this->origin == 'one') {
            return $this->redirect('/post/' . $this->entry->id, navigate: true);
        } elseif ($this->origin == 'admin') {
            return $this->redirect('/admin/posts', navigate: true);
        } else {
            return $this->redirect('/dashboard', navigate: true);
        }
    }

    public function update()
    {
        // Authorization check again before update (good practice)
        $this->authorize('update', $this->entry);

        $this->validate([
            'title' => 'required|min:3|max:255',
            'content' => 'required|min:50',
            'expiryDate' => 'required|date|after:today|before_or_equal:+2 years',
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

        $this->entry->update([
            'title' => $this->title,
            'content' => $this->content,
            'expiry_date' => $this->expiryDate,
            'from_date' => $this->fromDate,
            'to_date' => $this->toDate,
            'country' => $this->country,
            'city' => $this->city,
        ]);

        session()->flash('success', 'Post successfully updated.');

        // Redirect based on origin
        $this->redirectToCorrectPage();
    }

    public function render()
    {
        return view('livewire.post.form-post', [
            'action' => $this->action,
            'buttonText' => $this->buttonText,
        ]);
    }
}
