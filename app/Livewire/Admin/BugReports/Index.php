<?php

namespace App\Livewire\Admin\BugReports;

use Livewire\Component;
use App\Models\BugReport;
use App\Notifications\BugReportReviewed;
use Livewire\Attributes\{layout, middleware, title};
use Illuminate\Support\Facades\Notification;

#[Layout('components.layouts.admin.header')]
#[Title('Admin - Bug Reports')]
#[Middleware(['auth', 'admin_or_moderator'])]
class Index extends Component
{
    public $reports;

    public function mount()
    {
        $this->loadReports();
    }

    public function loadReports()
    {
        $this->reports = BugReport::where('status', 'pending')->latest()->get();
    }

    public function accept($id)
    {
        $report = BugReport::findOrFail($id);
        $report->status = 'accepted';
        $report->save();

        $this->notifyReporter($report);
        $this->loadReports();
    }

    public function reject($id)
    {
        $report = BugReport::findOrFail($id);
        $report->status = 'rejected';
        $report->save();

        $this->notifyReporter($report);
        $this->loadReports();
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
        return view('livewire.admin.bug-reports.index');
    }
}
