<?php

namespace App\Livewire;

use App\Models\PostReport;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ReportPostModal extends Component
{
    public bool $showModal = false;
    public ?int $postId = null;
    public string $postTitle = '';
    public string $reason = '';

    #[On('openReportModal')]
    public function openModal(int $postId, string $postTitle): void
    {
        $this->postId = $postId;
        $this->postTitle = $postTitle;
        $this->reason = ''; // Reset reason
        $this->resetErrorBag(); // Clear previous errors
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->postId = null;
        $this->postTitle = '';
        $this->reason = '';
    }

    public function submitReport(): void
    {
        if (!Auth::check() || !$this->postId) {
            // Basic check, ideally handled by only showing button when logged in
            $this->closeModal();
            return;
        }

        $this->validate([
            'reason' => 'nullable|string|max:1000', // Optional reason, max 1000 chars
        ]);

        // Check if user has already reported this post
        $existingReport = PostReport::where('user_id', Auth::id())
            ->where('post_id', $this->postId)
            ->where('status', 'pending') // Only count pending ones for re-reporting prevention
            ->exists();

        if ($existingReport) {
            $this->addError('general', 'You have already reported this post.');
            // Optionally close modal or just show error
            // $this->closeModal();
            // $this->dispatch('notify', ['message' => 'You have already reported this post.', 'type' => 'warning']);
            return;
        }


        PostReport::create([
            'user_id' => Auth::id(),
            'post_id' => $this->postId,
            'reason' => $this->reason ?: null, // Save null if empty
            'status' => 'pending',
        ]);

        $this->closeModal();

        // Dispatch a browser event for a success notification (e.g., using Alpine Toast or similar)
        $this->dispatch('notify', ['message' => 'Post reported successfully.', 'type' => 'success']);
    }

    public function render()
    {
        return view('livewire.report-post-modal');
    }
}