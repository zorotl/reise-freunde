<?php

namespace App\Livewire\Post;

use Livewire\Component;
use App\Models\Post;

class EditPost extends Component
{
    public Post $entry;
    public $title;
    public $content;
    public $expiryDate;
    public $fromDate;
    public $toDate;
    public $country;
    public $city;
    public $origin = 'all';

    public function mount(Post $id, $origin = 'all')
    {
        $this->origin = $origin;

        $this->entry = $id;
        $this->title = $this->entry->title;
        $this->content = $this->entry->content;
        $this->expiryDate = $this->entry->expiry_date ? $this->entry->expiry_date->format('Y-m-d') : null;
        $this->fromDate = $this->entry->from_date ? $this->entry->from_date->format('Y-m-d') : null;
        $this->toDate = $this->entry->to_date ? $this->entry->to_date->format('Y-m-d') : null;
        $this->country = $this->entry->country;
        $this->city = $this->entry->city;
    }

    public function update()
    {
        $this->validate([
            'title' => 'required|max:255',
            'content' => 'required|min:50',
            'expiryDate' => 'required|date|after:today|before_or_equal:+2 years',
            'fromDate' => 'required|date|after:today|before_or_equal:+1 years|before:toDate',
            'toDate' => 'required|date|after:today|before_or_equal:+2 years|after:fromDate',
            'country' => 'nullable|string|max:255',
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
        if ($this->origin === 'my') {
            $this->redirect('/myown', navigate: true);
        } else {
            $this->redirect('/post/show', navigate: true);
        }
    }

    public function render()
    {
        return view('livewire.post.form-post', [
            'action' => 'update',
            'buttonText' => 'Update Post',
        ]);
    }
}
