<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\BugReport;

class BugReportForm extends Component
{
    public ?string $email = null;
    public string $message = '';
    public string $url = '';

    public function mount()
    {
        if (auth()->check()) {
            $this->email = auth()->user()->email;
        }
        $this->url = request()->fullUrl();
    }

    public function submit()
    {
        $this->validate([
            'email' => 'nullable|email',
            'message' => 'required|string|max:1000',
            'url' => 'nullable|string|max:255',
        ]);

        BugReport::create([
            'user_id' => auth()->id(),
            'email' => $this->email,
            'message' => $this->message,
            'url' => $this->url,
            'status' => 'pending',
        ]);

        $this->reset('message');
        session()->flash('success', __('Thanks for your feedback!'));
    }

    public function render()
    {
        return view('livewire.bug-report-form');
    }
}
