<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\UserVerification;

class Verify extends Component
{
    use WithFileUploads;

    public $idDocument;
    public $socialLinks = [];
    public $note = '';

    public function mount()
    {
        $existing = auth()->user()->verification;
        if ($existing) {
            $this->note = $existing->note;
            $this->socialLinks = $existing->social_links ?? [];
        }
    }

    public function submit()
    {
        $data = [
            'note' => $this->note,
            'social_links' => $this->socialLinks,
            'status' => 'pending',
        ];

        if ($this->idDocument) {
            $data['id_document_path'] = $this->idDocument->store('verifications', 'public');
        }

        auth()->user()->verification()->updateOrCreate([], $data);

        session()->flash('success', __('Your verification data has been submitted.'));
    }

    public function render()
    {
        return view('livewire.profile.verify');
    }
}
