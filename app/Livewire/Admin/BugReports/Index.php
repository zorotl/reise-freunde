<?php

namespace App\Livewire\Admin\BugReports;

use App\Models\BugReport;
use App\Notifications\BugReportReviewed;
use Illuminate\Support\Facades\Notification;
use Livewire\Attributes\{Layout, Middleware, Title};
use Livewire\Component;

#[Layout('components.layouts.admin.header')]
#[Title('Admin - Bug Reports')]
#[Middleware(['auth', 'admin_or_moderator'])]
class Index extends Component
{
    public function accept($id)
    {
        $report = BugReport::findOrFail($id);
        $report->update(['status' => 'accepted']);
        $this->notifyReporter($report);
        session()->flash('message', __('Bug report accepted.'));
    }

    public function reject($id)
    {
        $report = BugReport::findOrFail($id);
        $report->update(['status' => 'rejected']);
        $this->notifyReporter($report);
        session()->flash('message', __('Bug report rejected.'));
    }

    protected function notifyReporter(BugReport $report): void
    {
        if ($report->user) {
            $report->user->notify(new BugReportReviewed($report->status));
        } elseif ($report->email) {
            Notification::route('mail', $report->email)->notify(new BugReportReviewed($report->status));
        }
    }

    public function render()
    {
        return view('livewire.admin.bug-reports.index', [
            'reports' => BugReport::with('user')->where('status', 'pending')->latest()->paginate(15),
        ]);
    }
}
