<?php

namespace App\Livewire;

use App\Models\Report;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

class ReportUserModal extends Component
{
    public bool $showModal = false;
    public ?int $userId = null;
    public string $username = '';
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

    #[On('openReportUserModal')]
    public function openModal(int $userId, string $username): void
    {
        $this->userId = $userId;
        $this->username = $username;
        $this->reason = '';
        $this->comment = '';
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['userId', 'username', 'reason', 'comment']);
    }

    public function submitReport(): void
    {
        if (!Auth::check() || !$this->userId) {
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
            ->where('reportable_type', \App\Models\User::class)
            ->where('reportable_id', $this->userId)
            ->where('status', 'pending')
            ->exists();

        if ($alreadyReported) {
            $this->addError('general', 'You have already reported this user.');
            return;
        }

        Report::create([
            'reporter_id' => Auth::id(),
            'reportable_id' => $this->userId,
            'reportable_type' => \App\Models\User::class,
            'reason' => $this->reason,
            'comment' => $this->comment ?: null,
            'status' => 'pending',
        ]);

        $this->closeModal();
        $this->dispatch('notify', ['message' => 'User reported successfully.', 'type' => 'success']);
    }

    public function render()
    {
        return view('livewire.report-user-modal');
    }
}
