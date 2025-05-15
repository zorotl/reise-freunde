<?php

namespace App\Livewire;

//use App\Models\PostReport;
use App\Models\Report;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ReportPostModal extends Component
{
    public bool $showModal = false;
    public ?int $postId = null;
    public string $postTitle = '';
    public string $reason = '';
    public string $comment = '';

    // Define valid reasons (can be moved to config later)
    public array $availableReasons = [
        'spam',
        'harassment',
        'hate_speech',
        'nudity',
        'misinformation',
        'other',
    ];

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
        $this->comment = '';
    }

    public function submitReport(): void
    {
        if (!Auth::check() || !$this->postId) {
            $this->closeModal();
            return;
        }

        $this->validate([
            'reason' => 'required|string|in:' . implode(',', $this->availableReasons),
            'comment' => 'nullable|string|max:1000',
        ]);

        // Check for existing pending report
        $alreadyReported = Report::where('reporter_id', Auth::id())
            ->where('reportable_type', Post::class)
            ->where('reportable_id', $this->postId)
            ->where('status', 'pending')
            ->exists();

        if ($alreadyReported) {
            $this->addError('general', 'You have already reported this post.');
            return;
        }

        Report::create([
            'reporter_id' => Auth::id(),
            'reportable_id' => $this->postId,
            'reportable_type' => Post::class,
            'reason' => $this->reason,
            'comment' => $this->comment ?: null,
            'status' => 'pending',
        ]);

        $this->closeModal();

        $this->dispatch('notify', [
            'message' => 'Post reported successfully.',
            'type' => 'success',
        ]);
    }

    public function render()
    {
        return view('livewire.report-post-modal');
    }
}