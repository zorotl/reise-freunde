<?php

namespace App\Livewire\Admin\Reports;

// use App\Models\PostReport;
use App\Models\Post;
use App\Models\User;
use App\Models\Report;
use App\Models\Message;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use App\Notifications\ReportResolved;

class ManageReports extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'pending'; // Default to pending reports
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 15;
    public string $reportType = 'post';

    protected $listeners = [
        'reportProcessed' => '$refresh', // Refresh list after processing
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    // Action to Accept a Report (Soft Delete Post & Redirect)
    public function acceptReport($id)
    {
        $report = Report::with('reportable')->findOrFail($id);

        $authorId = null;

        if ($report->reportable instanceof \App\Models\Post) {
            $authorId = $report->reportable->user_id;
            $report->reportable->delete(); // delete post
        }

        if ($report->reportable instanceof \App\Models\Message) {
            $authorId = $report->reportable->sender_id;
            $report->reportable->delete(); // delete message
        }

        if ($report->reportable instanceof \App\Models\User) {
            $authorId = $report->reportable->id; // the user being reported
            // no deletion, will be banned via modal
        }

        $report->status = 'accepted';
        $report->save();

        if ($authorId) {
            $this->dispatch('openEditModal', $authorId); // opens user modal
        }

        $this->dispatch('reportProcessed'); // refresh report list
    }


    // Action to Reject a Report
    public function rejectReport($id)
    {
        $report = Report::findOrFail($id);

        $report->update([
            'status' => 'rejected',
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        $report->reporter->notify(new ReportResolved($report));
        session()->flash('message', 'Report rejected.');
        $this->dispatch('reportProcessed');
    }

    public function render()
    {
        $reports = Report::with(['reporter', 'reportable'])
            ->when($this->reportType === 'post', fn($q) => $q->where('reportable_type', Post::class))
            ->when($this->reportType === 'user', fn($q) => $q->where('reportable_type', User::class))
            ->when($this->reportType === 'message', fn($q) => $q->where('reportable_type', Message::class))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('reason', 'like', '%' . $this->search . '%')
                        ->orWhere('comment', 'like', '%' . $this->search . '%')
                        ->orWhereHas('reporter', function ($subQ) {
                            $subQ->where('firstname', 'like', '%' . $this->search . '%')
                                ->orWhere('lastname', 'like', '%' . $this->search . '%')
                                ->orWhere('email', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHasMorph('reportable', [Post::class], function ($postQ) {
                            $postQ->where('title', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHasMorph('reportable', [Message::class], function ($msgQ) {
                            $msgQ->where('body', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.reports.manage-reports', [
            'reports' => $reports,
        ]);
    }
}