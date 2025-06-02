<?php

namespace App\Livewire;

use App\Models\Report;
use App\Models\Message;
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ReportMessageModal extends Component
{
    public bool $showModal = false;
    public ?int $messageId = null;
    public string $messageSnippet = '';
    public string $reason = '';
    public string $comment = '';

    public array $availableReasons = [
        'report_reason.spam',
        'report_reason.scam_or_fraud',
        'report_reason.inappropriate_content',
        'report_reason.inappropriate_behavior',
        'report_reason.harassment',
        'report_reason.hate_speech',
        'report_reason.misinformation',
        'report_reason.other',
    ];

    protected $listeners = ['openReportMessageModal'];

    public function openReportMessageModal(int $messageId, string $snippet): void
    {
        $this->messageId = $messageId;
        $this->messageSnippet = $snippet;
        $this->reason = '';
        $this->comment = '';
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['messageId', 'messageSnippet', 'reason', 'comment']);
    }

    public function submitReport(): void
    {
        $message = Message::findOrFail($this->messageId);

        // Block if user is not sender or receiver
        if (!in_array(Auth::id(), [$message->sender_id, $message->receiver_id])) {
            abort(403, 'Unauthorized to report this message.');
        }

        if (!Auth::check() || !$this->messageId) {
            $this->closeModal();
            return;
        }

        $this->validate([
            'reason' => 'required|string|in:' . implode(',', array_map(
                fn($r) => Str::after($r, 'report_reason.'),
                $this->availableReasons
            )),
            'comment' => 'nullable|string|max:1000',
        ]);

        $alreadyReported = Report::where('reporter_id', Auth::id())
            ->where('reportable_type', Message::class)
            ->where('reportable_id', $this->messageId)
            ->where('status', 'pending')
            ->exists();

        if ($alreadyReported) {
            $this->addError('general', 'You have already reported this message.');
            return;
        }

        Report::create([
            'reporter_id' => Auth::id(),
            'reportable_id' => $this->messageId,
            'reportable_type' => Message::class,
            'reason' => $this->reason,
            'comment' => $this->comment ?: null,
            'status' => 'pending',
        ]);

        $this->closeModal();

        $this->dispatch('notify', ['message' => 'Message reported successfully.', 'type' => 'success']);
    }

    public function render()
    {
        return view('livewire.report-message-modal');
    }
}
